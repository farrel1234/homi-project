// File: app/src/main/java/com/example/homi/data/repository/AccountRepository.kt
package com.example.homi.data.repository

import com.example.homi.data.model.ChangePasswordRequest
import com.example.homi.data.model.OkResponse
import com.example.homi.data.remote.ApiService
import com.google.gson.JsonParser
import okhttp3.ResponseBody
import retrofit2.HttpException
import retrofit2.Response

class AccountRepository(private val api: ApiService) {

    /**
     * CHANGE PASSWORD
     * Backend kamu pakai snake_case: current_password, new_password, new_password_confirmation
     * Jadi pemanggilan harus sesuai nama field di data class.
     */
    suspend fun changePassword(
        currentPassword: String,
        newPassword: String,
        newPasswordConfirmation: String
    ): OkResponse {
        return try {
            api.changePassword(
                ChangePasswordRequest(
                    current_password = currentPassword,
                    new_password = newPassword,
                    new_password_confirmation = newPasswordConfirmation
                )
            )
        } catch (e: HttpException) {
            throw Exception("Gagal ubah password. (${e.code()})")
        } catch (e: Exception) {
            throw Exception(e.message ?: "Gagal ubah password.")
        }
    }

    /**
     * GET NAME (dipakai AkunScreen)
     * Ambil nama user dari endpoint /me.
     * ApiService: @GET("me") suspend fun getMe(): Response<ResponseBody>
     */
    suspend fun fetchMyProfileName(): String {
        val res = api.getMe()
        if (!res.isSuccessful) {
            throw Exception("Gagal ambil profil. (${res.code()})")
        }

        val body = res.body() ?: throw Exception("Gagal ambil profil (body kosong).")
        val name = parseNameFromMe(body) ?: "Warga"
        return name
    }

    /**
     * UPDATE NAME (dipakai AkunScreen)
     * ApiService: @PUT("me") suspend fun updateMyProfile(@Body body: Map<String,String>): Response<ResponseBody>
     */
    suspend fun updateNameToServer(newName: String): String {
        val payload = mapOf("name" to newName)

        val res = api.updateMyProfile(payload)
        if (!res.isSuccessful) {
            throw Exception("Gagal update nama. (${res.code()})")
        }

        // kalau backend kamu balikin JSON user terbaru, kita ambil lagi nama dari respons
        val body = res.body()
        return if (body != null) {
            parseNameFromMe(body) ?: newName
        } else {
            newName
        }
    }

    /**
     * OPTIONAL HELPERS — kalau kamu butuh raw call di tempat lain.
     * (Aku biarin ada biar aman dan gak ngacak modul lain.)
     */
    suspend fun upsertResidentProfileMap(body: Map<String, String>): Response<OkResponse> {
        return api.upsertResidentProfileMap(body)
    }

    suspend fun getMyResidentProfileRaw(): Response<ResponseBody> {
        return api.getMyResidentProfileRaw()
    }

    suspend fun getMeRaw(): Response<ResponseBody> {
        return api.getMe()
    }

    suspend fun updateMyProfileRaw(body: Map<String, String>): Response<ResponseBody> {
        return api.updateMyProfile(body)
    }

    // =========================
    // JSON PARSER (robust)
    // =========================
    private fun parseNameFromMe(body: ResponseBody): String? {
        return try {
            val jsonStr = body.string()
            val root = JsonParser.parseString(jsonStr).asJsonObject

            // bentuk umum yang mungkin:
            // 1) { "name": "..." }
            // 2) { "data": { "name": "..." } }
            // 3) { "data": { "user": { "name": "..." } } }
            // 4) { "user": { "name": "..." } }

            fun getName(obj: com.google.gson.JsonObject?): String? {
                if (obj == null) return null
                val n = obj.get("name")
                if (n != null && n.isJsonPrimitive) return n.asString
                return null
            }

            // root.name
            getName(root)?.takeIf { it.isNotBlank() }?.let { return it }

            // root.user.name
            root.getAsJsonObject("user")?.let { u ->
                getName(u)?.takeIf { it.isNotBlank() }?.let { return it }
            }

            // root.data.name / root.data.user.name
            root.getAsJsonObject("data")?.let { d ->
                getName(d)?.takeIf { it.isNotBlank() }?.let { return it }

                d.getAsJsonObject("user")?.let { u ->
                    getName(u)?.takeIf { it.isNotBlank() }?.let { return it }
                }
            }

            null
        } catch (_: Exception) {
            null
        }
    }
}

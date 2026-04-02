// File: app/src/main/java/com/example/homi/data/repository/AccountRepository.kt
package com.example.homi.data.repository

import com.example.homi.data.model.*
import com.example.homi.data.remote.ApiService
import com.google.gson.Gson
import com.google.gson.JsonParser
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.ResponseBody
import retrofit2.HttpException
import retrofit2.Response

class AccountRepository(private val api: ApiService) {
    private val gson = Gson()

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

    suspend fun fetchMyProfileData(): Pair<String, String> {
        val res = api.getMe()
        if (!res.isSuccessful || res.body() == null) return Pair("Warga", "")

        val bodyString = res.body()!!.string()
        val json = JsonParser.parseString(bodyString).asJsonObject
        val data = json.getAsJsonObject("data") ?: json
        val userItem = data.getAsJsonObject("user") ?: data

        val name = userItem.get("full_name")?.asString ?: userItem.get("name")?.asString ?: "Warga"
        
        // Extract NIK from resident_profile inside data or user
        val residentProfile = data.getAsJsonObject("resident_profile") 
            ?: userItem.getAsJsonObject("resident_profile")
        val nik = residentProfile?.get("nik")?.asString ?: ""

        return Pair(name, nik)
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

    /**
     * FETCH FULL PROFILE (User + ResidentProfile)
     */
    suspend fun getFullProfile(): FullProfileResponse {
        val meRes = api.getMe()
        if (!meRes.isSuccessful) throw Exception("Gagal ambil profil user.")
        
        val meBody = meRes.body()?.string() ?: throw Exception("Profil user kosong.")
        val userJson = JsonParser.parseString(meBody).asJsonObject
        // API wrap JSON in "data" or "user"
        val dataPart = userJson.getAsJsonObject("data") ?: userJson
        val userObj = dataPart.getAsJsonObject("user") ?: dataPart
        
        val user = gson.fromJson(userObj, FullProfileResponse::class.java)
        
        // Helper to clean ISO date format (2024-01-01T... -> 2024-01-01)
        fun sanitizeDate(d: String?): String? {
            if (d.isNullOrBlank()) return d
            return if (d.contains("T")) d.substringBefore("T") else d
        }

        // Fetch Resident Profile (blok, no_rumah, etc)
        val profileRes = api.getMyResidentProfileRaw()
        if (profileRes.isSuccessful) {
            val profBody = profileRes.body()?.string()
            if (!profBody.isNullOrBlank()) {
                val profJson = JsonParser.parseString(profBody).asJsonObject
                val profData = profJson.getAsJsonObject("data") ?: profJson
                val residentPart = gson.fromJson(profData, ResidentProfileDto::class.java)
                
                // Sanitize dates
                val cleanedResident = residentPart.copy(
                    tanggalLahir = sanitizeDate(residentPart.tanggalLahir)
                )

                return user.copy(residentProfile = cleanedResident)
            }
        }
        
        return user
    }

    suspend fun updateFullProfile(
        fullName: String,
        phone: String,
        nik: String,
        blok: String,
        noRumah: String,
        rt: String,
        rw: String,
        namaRt: String,
        pekerjaan: String,
        tempatLahir: String,
        tanggalLahir: String,
        jenisKelamin: String,
        houseType: String
    ) {
        // 1. Update User basic info
        val userUpdate = mapOf(
            "name" to fullName,
            "full_name" to fullName,
            "phone" to phone
        )
        val res1 = api.updateMyProfile(userUpdate)
        if (!res1.isSuccessful) throw Exception("Gagal update info dasar user.")

        // 2. Update Resident Profile
        val residentUpdate = mutableMapOf<String, String>()
        residentUpdate["nik"] = nik
        residentUpdate["blok"] = blok
        residentUpdate["no_rumah"] = noRumah
        residentUpdate["rt"] = rt
        residentUpdate["rw"] = rw
        residentUpdate["nama_rt"] = namaRt
        residentUpdate["pekerjaan"] = pekerjaan
        residentUpdate["tempat_lahir"] = tempatLahir
        residentUpdate["tanggal_lahir"] = tanggalLahir
        residentUpdate["jenis_kelamin"] = jenisKelamin
        residentUpdate["house_type"] = houseType

        val res2 = api.upsertResidentProfileMap(residentUpdate)
        if (!res2.isSuccessful) throw Exception("Gagal update profil alamat.")
    }

    suspend fun isProfileComplete(): Boolean {
        return try {
            val profile = getFullProfile()
            val isUserComplete = !profile.fullName.isNullOrBlank() && 
                                 !profile.phone.isNullOrBlank()
            
            val rp = profile.residentProfile
            val isResidentComplete = rp != null &&
                                    !rp.nik.isNullOrBlank() &&
                                    !rp.blok.isNullOrBlank() &&
                                    !rp.noRumah.isNullOrBlank() &&
                                    !rp.rt.isNullOrBlank() &&
                                    !rp.rw.isNullOrBlank() &&
                                    !rp.namaRt.isNullOrBlank() &&
                                    !rp.pekerjaan.isNullOrBlank() &&
                                    !rp.tempatLahir.isNullOrBlank() &&
                                    !rp.tanggalLahir.isNullOrBlank() &&
                                    !rp.jenisKelamin.isNullOrBlank() &&
                                    !rp.houseType.isNullOrBlank()
            
            isUserComplete && isResidentComplete
        } catch (e: Exception) {
            false
        }
    }

    suspend fun updateProfilePhoto(file: java.io.File): String {
        val mediaType = "image/*".toMediaTypeOrNull()
        val requestFile = okhttp3.RequestBody.create(mediaType, file)
        val body = okhttp3.MultipartBody.Part.createFormData("photo", file.name, requestFile)

        val res = api.updateProfilePhoto(body)
        if (!res.isSuccessful) throw Exception("Gagal update foto profil. (${res.code()})")

        val jsonStr = res.body()?.string() ?: ""
        val root = JsonParser.parseString(jsonStr).asJsonObject
        val data = root.getAsJsonObject("data")
        return data.get("profile_photo_url").asString
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

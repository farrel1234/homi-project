package com.example.homi.data.repository

import com.example.homi.data.model.*
import com.example.homi.data.remote.ApiService
import retrofit2.HttpException
import retrofit2.Response

class AuthRepository(private val api: ApiService) {

    suspend fun login(email: String, password: String): ApiResponse<VerifyOtpData> {
        return try {
            api.login(LoginRequest(email, password))
        } catch (e: HttpException) {
            // biar ViewModel tetap bisa handle via res.success / res.message
            ApiResponse(
                success = false,
                message = "Login gagal. (${e.code()})",
                data = null
            )
        } catch (e: Exception) {
            ApiResponse(
                success = false,
                message = e.message ?: "Login gagal.",
                data = null
            )
        }
    }

    suspend fun register(name: String, email: String, password: String): RegisterData {
        return try {
            val res = api.register(RegisterRequest(name, email, password))
            if (res.success && res.data != null) res.data
            else throw Exception(res.message?.ifBlank { "Gagal daftar." } ?: "Gagal daftar.")
        } catch (e: HttpException) {
            throw Exception("Gagal daftar. (${e.code()})")
        }
    }

    suspend fun verifyOtp(email: String, otp: String): VerifyOtpData {
        return try {
            val res = api.verifyOtp(VerifyOtpRequest(email, otp))
            if (res.success && res.data != null) res.data
            else throw Exception(
                res.message?.ifBlank { "OTP salah / verifikasi gagal." }
                    ?: "OTP salah / verifikasi gagal."
            )
        } catch (e: HttpException) {
            throw Exception("Verifikasi OTP gagal. (${e.code()})")
        }
    }

    // dipakai di KonfirmasiDaftarScreen
    suspend fun saveNaiveBayesProfile(
        houseType: String,
        job: String,
        housing: String,
        block: String,
        houseNumber: String,
        upsertCall: suspend (Map<String, String>) -> Response<OkResponse>
    ) {
        val body = mapOf(
            "house_type" to houseType,
            "job" to job,
            "housing" to housing,
            "block" to block,
            "house_number" to houseNumber
        )

        val res = upsertCall(body)
        if (!res.isSuccessful) throw Exception("Gagal simpan profil. (${res.code()})")

        val ok = res.body()
        if (ok != null && ok.success == false) {
            throw Exception(ok.message?.ifBlank { "Gagal simpan profil." } ?: "Gagal simpan profil.")
        }
    }

    // GOOGLE LOGIN
    suspend fun loginGoogle(idToken: String): ApiResponse<VerifyOtpData> {
        return try {
            api.loginGoogle(GoogleLoginRequest(idToken = idToken))
        } catch (e: HttpException) {
            // biar ViewModel tetap bisa handle via res.success / res.message
            ApiResponse(
                success = false,
                message = "Login Google gagal. (${e.code()})",
                data = null
            )
        } catch (e: Exception) {
            ApiResponse(
                success = false,
                message = e.message ?: "Login Google gagal.",
                data = null
            )
        }
    }
}

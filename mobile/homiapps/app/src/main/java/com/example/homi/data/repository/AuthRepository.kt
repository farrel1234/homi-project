package com.example.homi.data.repository

import com.example.homi.data.model.*
import com.example.homi.data.remote.ApiService
import org.json.JSONObject
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
                message = extractApiErrorMessage(e, "Login gagal."),
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

    suspend fun register(name: String, email: String, password: String, tenantCode: String, googleId: String? = null): RegisterData {
        return try {
            val res = api.register(RegisterRequest(name, email, password, tenantCode, googleId))
            if (res.success && res.data != null) res.data
            else throw Exception(res.message?.ifBlank { "Gagal daftar." } ?: "Gagal daftar.")
        } catch (e: HttpException) {
            throw Exception(extractApiErrorMessage(e, "Gagal daftar."))
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
            throw Exception(extractApiErrorMessage(e, "Verifikasi OTP gagal."))
        }
    }

    suspend fun resendRegisterOtp(email: String) {
        try {
            val res = api.resendOtp(ForgotPasswordRequest(email))
            if (!res.success) {
                throw Exception(res.message?.ifBlank { "Gagal kirim ulang OTP." } ?: "Gagal kirim ulang OTP.")
            }
        } catch (e: HttpException) {
            throw Exception(extractApiErrorMessage(e, "Gagal kirim ulang OTP."))
        }
    }

    suspend fun forgotPassword(email: String) {
        try {
            val res = api.forgotPassword(ForgotPasswordRequest(email))
            if (!res.success) {
                throw Exception(res.message?.ifBlank { "Gagal mengirim OTP reset." } ?: "Gagal mengirim OTP reset.")
            }
        } catch (e: HttpException) {
            throw Exception(extractApiErrorMessage(e, "Gagal mengirim OTP reset."))
        }
    }

    suspend fun verifyResetOtp(email: String, otp: String): VerifyResetOtpData {
        return try {
            val res = api.verifyResetOtp(VerifyOtpRequest(email, otp))
            if (res.success && res.data != null) res.data
            else throw Exception(
                res.message?.ifBlank { "OTP reset tidak valid." }
                    ?: "OTP reset tidak valid."
            )
        } catch (e: HttpException) {
            throw Exception(extractApiErrorMessage(e, "Verifikasi OTP reset gagal."))
        }
    }

    suspend fun resetPassword(resetToken: String, password: String, passwordConfirmation: String) {
        try {
            val res = api.resetPassword(
                ResetPasswordRequest(
                    resetToken = resetToken,
                    password = password,
                    passwordConfirmation = passwordConfirmation
                )
            )
            if (!res.success) {
                throw Exception(res.message?.ifBlank { "Reset password gagal." } ?: "Reset password gagal.")
            }
        } catch (e: HttpException) {
            throw Exception(extractApiErrorMessage(e, "Reset password gagal."))
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
            // We need raw response to check needs_registration
            val rawResponse = api.loginGoogleRaw(GoogleLoginRequest(idToken = idToken))
            val body = rawResponse.body()?.string() ?: ""
            val json = JSONObject(body)

            val needsReg = json.optBoolean("needs_registration", false)
            if (needsReg) {
                val email = json.optString("google_email", "")
                val name = json.optString("google_name", "")
                val gid = json.optString("google_id", "")
                // Encode needs_registration data in message with a special prefix
                ApiResponse(
                    success = false,
                    message = "NEEDS_REG|$email|$name|$gid",
                    data = null
                )
            } else if (json.optBoolean("success", false)) {
                val dataObj = json.optJSONObject("data")
                val userObj = dataObj?.optJSONObject("user")
                val token = dataObj?.optString("token", "") ?: ""
                val userName = userObj?.optString("name", "Warga") ?: "Warga"
                val userId = userObj?.optLong("id", 0) ?: 0
                val userEmail = userObj?.optString("email", "") ?: ""

                ApiResponse(
                    success = true,
                    message = json.optString("message", "Login Google berhasil"),
                    data = VerifyOtpData(
                        token = token,
                        user = UserDto(id = userId, name = userName, email = userEmail)
                    )
                )
            } else {
                ApiResponse(
                    success = false,
                    message = json.optString("message", "Login Google gagal."),
                    data = null
                )
            }
        } catch (e: HttpException) {
            ApiResponse(
                success = false,
                message = extractApiErrorMessage(e, "Login Google gagal."),
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

    suspend fun getPublicTenants(): TenantListResponse {
        return try {
            api.getPublicTenants()
        } catch (e: Exception) {
            TenantListResponse(success = false, data = emptyList(), message = e.message)
        }
    }

    private fun extractApiErrorMessage(e: HttpException, fallback: String): String {
        return try {
            val raw = e.response()?.errorBody()?.string().orEmpty()
            val msg = if (raw.isBlank()) null else JSONObject(raw).optString("message", null)
            msg?.takeIf { it.isNotBlank() } ?: "$fallback (${e.code()})"
        } catch (_: Exception) {
            "$fallback (${e.code()})"
        }
    }
}

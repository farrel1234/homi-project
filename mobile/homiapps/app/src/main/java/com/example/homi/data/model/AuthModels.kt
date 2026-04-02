package com.example.homi.data.model

import com.google.gson.annotations.SerializedName
data class LoginRequest(
    val email: String,
    val password: String
)

data class RegisterRequest(
    val name: String,
    val email: String,
    val password: String,
    @SerializedName("tenant_code") val tenantCode: String,
    @SerializedName("google_id") val googleId: String? = null
)

data class VerifyOtpRequest(
    val email: String,
    val otp: String
)

data class ForgotPasswordRequest(
    val email: String
)

data class ResetPasswordRequest(
    @SerializedName("reset_token") val resetToken: String,
    val password: String,
    @SerializedName("password_confirmation") val passwordConfirmation: String
)

data class ForgotPasswordData(
    val email: String? = null
)

data class VerifyResetOtpData(
    val email: String? = null,
    @SerializedName("reset_token") val resetToken: String
)

// backend return data.user + data.otp (DEV) saat register
data class RegisterData(
    val user: UserDto,
    val otp: Int? = null
)

// backend return data.user + data.token saat login / verifyOtp / loginGoogle
data class VerifyOtpData(
    val user: UserDto,
    val token: String
)

data class UserDto(
    val id: Long,
    val name: String,
    val email: String
)

// GOOGLE LOGIN
data class GoogleLoginRequest(
    @SerializedName("id_token") val idToken: String
)

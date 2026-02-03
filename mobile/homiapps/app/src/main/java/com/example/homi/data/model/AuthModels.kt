package com.example.homi.data.model

import com.google.gson.annotations.SerializedName

data class LoginRequest(
    val email: String,
    val password: String
)

data class RegisterRequest(
    val name: String,
    val email: String,
    val password: String
)

data class VerifyOtpRequest(
    val email: String,
    val otp: String
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

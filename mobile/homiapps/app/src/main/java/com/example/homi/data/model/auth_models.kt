package com.example.homi.data.model

data class LoginRequest(
    val email: String,
    val password: String
)

data class LoginResponse(
    val success: Boolean,
    val message: String,
    val data: LoginData?
)

data class LoginData(
    val token: String,
    val user: UserDto
)

data class UserDto(
    val id: Long,
    val name: String,
    val email: String,
    val role: String,
)

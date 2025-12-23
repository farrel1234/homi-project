package com.example.homi.data.repository

import com.example.homi.data.model.LoginRequest
import com.example.homi.data.remote.ApiService


class AuthRepository(private val api: ApiService) {
    suspend fun login(email: String, password: String) =
        api.login(LoginRequest(email, password))
}

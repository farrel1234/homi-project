// File: com/example/homi/data/remote/AuthInterceptor.kt
package com.example.homi.data.remote

import kotlinx.coroutines.runBlocking
import okhttp3.Interceptor
import okhttp3.Response

class AuthInterceptor(
    private val tokenProvider: suspend () -> String?
) : Interceptor {

    override fun intercept(chain: Interceptor.Chain): Response {
        val original = chain.request()
        val token = runBlocking { tokenProvider() }?.trim()

        val req = if (!token.isNullOrEmpty()) {
            original.newBuilder()
                .header("Authorization", "Bearer $token") // pakai header biar replace
                .build()
        } else {
            original
        }

        return chain.proceed(req)
    }
}

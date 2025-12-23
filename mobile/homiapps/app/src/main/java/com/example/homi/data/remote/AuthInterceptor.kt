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

        val builder = original.newBuilder()
            .header("Accept", "application/json")

        if (!token.isNullOrBlank()) {
            // kalau tokenStore kamu menyimpan token mentah, ini sudah benar
            builder.header("Authorization", "Bearer $token")
        }

        return chain.proceed(builder.build())
    }
}

// File: com/example/homi/data/remote/AuthInterceptor.kt
package com.example.homi.data.remote

import kotlinx.coroutines.runBlocking
import okhttp3.Interceptor
import okhttp3.Response

class AuthInterceptor(
    private val tokenProvider: suspend () -> String?,
    private val tenantCodeProvider: suspend () -> String?
) : Interceptor {

    override fun intercept(chain: Interceptor.Chain): Response {
        val original = chain.request()
        val token = runBlocking { tokenProvider() }?.trim()
        val tenantCode = runBlocking { tenantCodeProvider() }?.trim()

        val builder = original.newBuilder()

        // 1. Tambahkan Token jika ada
        if (!token.isNullOrEmpty()) {
            builder.header("Authorization", "Bearer $token")
        }

        // 2. Tambahkan X-Tenant-Code jika ada, fallback ke config jika kosong
        val finalTenantCode = tenantCode?.takeIf { it.isNotEmpty() } ?: ApiConfig.DEFAULT_TENANT_CODE
        builder.header(ApiConfig.TENANT_HEADER, finalTenantCode)

        return chain.proceed(builder.build())
    }
}

// File: ApiConfig.kt
package com.example.homi.data.remote

object ApiConfig {
    // Host laravel
    const val HOST = "http://10.0.2.2:8000"

    // Retrofit base url
    const val BASE_URL = "http://10.0.2.2:8000/api/"

    const val TENANT_HEADER = "X-Tenant-Code"
    const val DEFAULT_TENANT_CODE = "hawaii-garden"

    @Volatile
    var tenantCode: String = DEFAULT_TENANT_CODE
}

// File: ApiConfig.kt
package com.example.homi.data.remote

object ApiConfig {
    // === KONFIGURASI SERVER ===
    // Untuk HP fisik (WiFi lokal): gunakan IP komputermu
    // Untuk emulator Android Studio: gunakan 10.0.2.2
    // Untuk produksi/server: gunakan URL domain (https://...)

    const val HOST = "http://10.0.2.2:8001"
    const val BASE_URL = "http://10.0.2.2:8001/api/"

    const val TENANT_HEADER = "X-Tenant-Code"
    const val DEFAULT_TENANT_CODE = ""

    @Volatile
    var tenantCode: String = DEFAULT_TENANT_CODE
}

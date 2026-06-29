// File: ApiConfig.kt
package com.example.homi.data.remote

object ApiConfig {
    // === KONFIGURASI SERVER ===
    // Untuk HP fisik (WiFi lokal): gunakan IP komputermu
    // Untuk emulator Android Studio: gunakan 10.0.2.2
    // Untuk produksi/server: gunakan URL domain (https://...)

    // ✅ URL Production — besthomi.online
    // Untuk emulator Android Studio (development only): gunakan "http://10.0.2.2:8000"
    const val HOST = "https://besthomi.online"
    const val BASE_URL = "https://besthomi.online/api/"

    const val TENANT_HEADER = "X-Tenant-Code"
    const val DEFAULT_TENANT_CODE = ""

    @Volatile
    var tenantCode: String = DEFAULT_TENANT_CODE
}

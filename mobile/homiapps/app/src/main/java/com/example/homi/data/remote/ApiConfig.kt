// File: ApiConfig.kt
package com.example.homi.data.remote

object ApiConfig {
    // === KONFIGURASI SERVER ===
    // Untuk HP fisik (WiFi lokal): gunakan IP komputermu
    // Untuk emulator Android Studio: gunakan 10.0.2.2
    // Untuk produksi/server: gunakan URL domain (https://...)

    // ✅ URL Production — besthomi.online
    const val HOST = "https://besthomi.online"
    const val BASE_URL = "https://besthomi.online/api/"

    // === URL Lokal (untuk development saja, uncomment jika perlu test lokal) ===
    // const val HOST = "http://10.0.2.2:8000"          // Emulator Android Studio
    // const val BASE_URL = "http://10.0.2.2:8000/api/" // Emulator Android Studio
    // const val HOST = "http://192.168.x.x:8000"       // HP fisik via WiFi (ganti IP)
    // const val BASE_URL = "http://192.168.x.x:8000/api/"

    const val TENANT_HEADER = "X-Tenant-Code"
    const val DEFAULT_TENANT_CODE = ""

    @Volatile
    var tenantCode: String = DEFAULT_TENANT_CODE
}

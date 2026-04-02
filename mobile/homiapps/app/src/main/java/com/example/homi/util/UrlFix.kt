package com.example.homi.util

fun fixLocalhostUrl(url: String?): String? {
    if (url.isNullOrBlank()) return url
    return url
        .replace("http://127.0.0.1:8000", com.example.homi.data.remote.ApiConfig.HOST)
        .replace("http://localhost:8000", com.example.homi.data.remote.ApiConfig.HOST)
        .replace("http://127.0.0.1", com.example.homi.data.remote.ApiConfig.HOST)
        .replace("http://localhost", com.example.homi.data.remote.ApiConfig.HOST)
}

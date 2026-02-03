package com.example.homi.utils

fun fixLocalhostUrl(url: String?): String? {
    if (url.isNullOrBlank()) return url
    return url
        .replace("http://127.0.0.1:", "http://10.0.2.2:")
        .replace("http://localhost:", "http://10.0.2.2:")
}

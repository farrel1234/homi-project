// File: UrlUtil.kt
package com.example.homi.util

import com.example.homi.data.remote.ApiConfig

object UrlUtil {

    /**
     * Kalau backend ngasih path relatif (mis: "storage/qrcodes/x.png"),
     * jadiin full url: "http://...:8000/storage/qrcodes/x.png"
     *
     * Kalau null/blank -> return null (biar gak request ke "/")
     */
    fun fileUrl(path: String?): String? {
        val p = path?.trim().orEmpty()
        if (p.isBlank()) return null

        // kalau backend sudah kasih full url
        if (p.startsWith("http://") || p.startsWith("https://")) return p

        return ApiConfig.HOST.trimEnd('/') + "/" + p.trimStart('/')
    }
}

package com.example.homi.util

import java.time.Instant
import java.time.ZoneId
import java.time.format.DateTimeFormatter
import java.util.Locale
import java.text.SimpleDateFormat
import java.util.Date

object DateUtils {
    fun todayIso(): String {
        val sdf = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        return sdf.format(Date())
    }

    /**
     * Convert ISO date (termasuk yang ada microseconds "000000Z") ke format ID yang enak dibaca.
     * contoh: 2026-01-07T14:06:13.000000Z -> 07 Jan 2026, 21:06 (tergantung timezone device)
     */
    fun formatIsoToId(iso: String?): String {
        if (iso.isNullOrBlank()) return ""

        // beberapa backend kasih format "2025-12-31 12:34:11" (non-ISO)
        // kita coba handle dua format: ISO (Instant.parse) & "yyyy-MM-dd HH:mm:ss"
        return try {
            val cleaned = iso
                .trim()
                .replace("000000Z", "Z") // handle microseconds panjang
            val instant = Instant.parse(cleaned)
            val formatter = DateTimeFormatter
                .ofPattern("dd MMM yyyy, HH:mm", Locale("id", "ID"))
                .withZone(ZoneId.systemDefault())
            formatter.format(instant)
        } catch (_: Exception) {
            // fallback untuk format "yyyy-MM-dd HH:mm:ss"
            try {
                val formatterIn = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss", Locale.US)
                val localDateTime = java.time.LocalDateTime.parse(iso.trim(), formatterIn)
                val formatterOut = DateTimeFormatter.ofPattern("dd MMM yyyy, HH:mm", Locale("id", "ID"))
                localDateTime.format(formatterOut)
            } catch (_: Exception) {
                iso
            }
        }
    }
}

package com.example.homi.util

import android.content.Context
import android.content.Intent
import android.net.Uri
import android.widget.Toast
import java.net.URLEncoder

object WhatsAppUtil {

    /**
     * phoneInternational: contoh "6281992440287" (tanpa +, tanpa spasi, tanpa 0 depan)
     */
    fun openChat(context: Context, phoneInternational: String, message: String) {
        val phone = phoneInternational
            .trim()
            .replace("+", "")
            .replace(" ", "")

        val encoded = URLEncoder.encode(message, "UTF-8")

        // 1) Coba paksa buka WhatsApp app
        val waUri = Uri.parse("https://wa.me/$phone?text=$encoded")
        val waIntent = Intent(Intent.ACTION_VIEW, waUri).apply {
            setPackage("com.whatsapp")
            addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
        }

        try {
            context.startActivity(waIntent)
            return
        } catch (_: Exception) {
            // 2) Fallback: buka link wa.me pakai app apapun (browser juga boleh)
            val fallback = Intent(Intent.ACTION_VIEW, waUri).apply {
                addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            }
            try {
                context.startActivity(fallback)
            } catch (e2: Exception) {
                Toast.makeText(context, "Gagal membuka WhatsApp: ${e2.message}", Toast.LENGTH_SHORT).show()
            }
        }
    }
}

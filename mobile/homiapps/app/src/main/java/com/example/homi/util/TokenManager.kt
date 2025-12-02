package com.example.homi.util

import android.content.Context
import android.content.SharedPreferences

class TokenManager(context: Context) {

    private val prefs: SharedPreferences =
        context.getSharedPreferences("homi_prefs", Context.MODE_PRIVATE)

    fun saveAccessToken(token: String?) {
        prefs.edit().putString("access_token", token).apply()
    }

    fun saveRefreshToken(token: String?) {
        prefs.edit().putString("refresh_token", token).apply()
    }

    fun getAccessToken(): String? = prefs.getString("access_token", null)
    fun getRefreshToken(): String? = prefs.getString("refresh_token", null)

    fun clear() {
        prefs.edit().clear().apply()
    }
}

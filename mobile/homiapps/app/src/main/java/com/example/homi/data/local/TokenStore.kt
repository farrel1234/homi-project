package com.example.homi.data.local

import android.content.Context
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import androidx.datastore.preferences.preferencesDataStore
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map

private val Context.dataStore by preferencesDataStore(name = "auth_store")

class TokenStore(private val context: Context) {

    private val KEY_TOKEN = stringPreferencesKey("token")
    private val KEY_NAME = stringPreferencesKey("name")

    val tokenFlow: Flow<String?> =
        context.dataStore.data.map { prefs -> prefs[KEY_TOKEN] }

    val nameFlow: Flow<String?> =
        context.dataStore.data.map { prefs -> prefs[KEY_NAME] }

    suspend fun saveToken(token: String) {
        context.dataStore.edit { prefs -> prefs[KEY_TOKEN] = token }
    }

    suspend fun saveName(name: String) {
        context.dataStore.edit { prefs -> prefs[KEY_NAME] = name }
    }

    suspend fun clear() {
        context.dataStore.edit { prefs ->
            prefs.remove(KEY_TOKEN)
            prefs.remove(KEY_NAME)
        }
    }
}

package com.example.homi.data.local

import android.content.Context
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import androidx.datastore.preferences.preferencesDataStore
import com.example.homi.data.remote.ApiConfig
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map

private val Context.dataStore by preferencesDataStore(name = "auth_store")

class TokenStore(private val context: Context) {

    private val KEY_TOKEN = stringPreferencesKey("token")
    private val KEY_NAME = stringPreferencesKey("name")
    private val KEY_NIK = stringPreferencesKey("nik")
    private val KEY_TENANT_CODE = stringPreferencesKey("tenant_code")
    private val KEY_TENANT_NAME = stringPreferencesKey("tenant_name")
    private val KEY_HAS_SEEN_ONBOARDING = androidx.datastore.preferences.core.booleanPreferencesKey("has_seen_onboarding")

    val tokenFlow: Flow<String?> =
        context.dataStore.data.map { prefs -> prefs[KEY_TOKEN] }

    val nameFlow: Flow<String?> =
        context.dataStore.data.map { prefs -> prefs[KEY_NAME] }

    val nikFlow: Flow<String?> =
        context.dataStore.data.map { prefs -> prefs[KEY_NIK] }

    val tenantNameFlow: Flow<String?> =
        context.dataStore.data.map { prefs -> prefs[KEY_TENANT_NAME] }

    val hasSeenOnboardingFlow: Flow<Boolean> =
        context.dataStore.data.map { prefs -> prefs[KEY_HAS_SEEN_ONBOARDING] ?: false }

    val tenantCodeFlow: Flow<String> =
        context.dataStore.data.map { prefs ->
            prefs[KEY_TENANT_CODE]?.trim().takeUnless { it.isNullOrEmpty() }
                ?: ApiConfig.DEFAULT_TENANT_CODE
        }

    suspend fun saveToken(token: String) {
        context.dataStore.edit { prefs -> prefs[KEY_TOKEN] = token }
    }

    suspend fun saveName(name: String) {
        context.dataStore.edit { prefs -> prefs[KEY_NAME] = name }
    }

    suspend fun saveNik(nik: String) {
        context.dataStore.edit { prefs -> prefs[KEY_NIK] = nik }
    }

    suspend fun saveTenantName(name: String) {
        context.dataStore.edit { prefs -> prefs[KEY_TENANT_NAME] = name }
    }

    suspend fun saveTenantCode(code: String) {
        val normalized = code.trim()
        if (normalized.isBlank()) return
        context.dataStore.edit { prefs -> prefs[KEY_TENANT_CODE] = normalized }
    }

    suspend fun saveHasSeenOnboarding(hasSeen: Boolean) {
        context.dataStore.edit { prefs -> prefs[KEY_HAS_SEEN_ONBOARDING] = hasSeen }
    }

    suspend fun clear() {
        context.dataStore.edit { prefs ->
            prefs.remove(KEY_TOKEN)
            prefs.remove(KEY_NAME)
            prefs.remove(KEY_NIK)
        }
    }
}

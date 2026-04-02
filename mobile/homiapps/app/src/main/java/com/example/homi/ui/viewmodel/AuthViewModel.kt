package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.homi.data.local.TokenStore
import com.example.homi.data.remote.ApiConfig
import com.example.homi.data.repository.AuthRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.launch

data class AuthState(
    val loading: Boolean = false,
    val error: String? = null,
    val isLoggedIn: Boolean = false,
    val userName: String = "Warga",
    val tenantCode: String = ApiConfig.DEFAULT_TENANT_CODE,
    // Google Sign-Up redirect
    val needsGoogleRegister: Boolean = false,
    val googleEmail: String = "",
    val googleName: String = "",
    val googleId: String = "",
    // Tenant list for dropdown
    val tenants: List<com.example.homi.data.model.TenantListData> = emptyList()
)

class AuthViewModel(
    private val repo: AuthRepository,
    private val tokenStore: TokenStore
) : ViewModel() {

    private val _state = MutableStateFlow(AuthState())
    val state = _state.asStateFlow()

    init {
        viewModelScope.launch {
            val storedCode = runCatching { tokenStore.tenantCodeFlow.first() }
                .getOrNull()
                .orEmpty()
                .trim()
                .ifBlank { ApiConfig.DEFAULT_TENANT_CODE }

            ApiConfig.tenantCode = storedCode
            tokenStore.saveTenantCode(storedCode)

            _state.value = _state.value.copy(tenantCode = storedCode)
            
            // Fetch public tenants for dropdown
            fetchTenants()
        }
    }

    fun fetchTenants() {
        viewModelScope.launch {
            val res = repo.getPublicTenants()
            if (res.success) {
                _state.value = _state.value.copy(tenants = res.data)
            }
        }
    }

    fun setTenantCode(rawCode: String) {
        val normalized = rawCode.trim()
            .lowercase()
            .replace("\\s+".toRegex(), "-")
            .replace("[^a-z0-9-]".toRegex(), "")
            .ifBlank { ApiConfig.DEFAULT_TENANT_CODE }

        ApiConfig.tenantCode = normalized
        _state.value = _state.value.copy(tenantCode = normalized)

        viewModelScope.launch {
            tokenStore.saveTenantCode(normalized)
        }
    }

    fun login(email: String, password: String) {
        viewModelScope.launch {
            _state.value = _state.value.copy(loading = true, error = null)
            try {
                val res = repo.login(email, password)

                if (res.success && res.data != null) {
                    val token = res.data.token
                    val serverName = res.data.user.name

                    tokenStore.saveToken(token)

                    val existingName = runCatching { tokenStore.nameFlow.first() }.getOrNull()

                    val nameToSave =
                        serverName.trim().takeIf { it.isNotBlank() }
                            ?: existingName?.trim().takeIf { !it.isNullOrBlank() }
                            ?: "Warga"

                    tokenStore.saveName(nameToSave)

                    _state.value = AuthState(
                        loading = false,
                        isLoggedIn = true,
                        userName = nameToSave
                    )
                } else {
                    _state.value = _state.value.copy(
                        loading = false,
                        error = res.message ?: "Login gagal"
                    )
                }
            } catch (e: Exception) {
                _state.value = _state.value.copy(
                    loading = false,
                    error = e.message ?: "Error login"
                )
            }
        }
    }

    fun loginGoogle(idToken: String) {
        viewModelScope.launch {
            _state.value = _state.value.copy(loading = true, error = null, needsGoogleRegister = false)
            try {
                val res = repo.loginGoogle(idToken)

                if (res.success && res.data != null) {
                    val token = res.data.token
                    val serverName = res.data.user.name

                    tokenStore.saveToken(token)
                    tokenStore.saveName(serverName.ifBlank { "Warga" })

                    _state.value = AuthState(
                        loading = false,
                        isLoggedIn = true,
                        userName = serverName.ifBlank { "Warga" }
                    )
                } else {
                    // Check if needs_registration from raw response
                    val msg = res.message ?: "Login Google gagal"
                    if (msg.startsWith("NEEDS_REG|")) {
                        val parts = msg.split("|")
                        if (parts.size >= 4) {
                            val email = parts[1]
                            val name = parts[2]
                            val gid = parts[3]
                            setGoogleNeedsRegister(email, name, gid)
                        } else {
                            _state.value = _state.value.copy(loading = false, error = "Format data Google tidak valid")
                        }
                    } else {
                        _state.value = _state.value.copy(
                            loading = false,
                            error = msg
                        )
                    }
                }
            } catch (e: Exception) {
                _state.value = _state.value.copy(
                    loading = false,
                    error = e.message ?: "Login Google gagal"
                )
            }
        }
    }

    fun setGoogleNeedsRegister(email: String, name: String, googleId: String) {
        _state.value = _state.value.copy(
            loading = false,
            error = null,
            needsGoogleRegister = true,
            googleEmail = email,
            googleName = name,
            googleId = googleId
        )
    }

    fun clearGoogleRegister() {
        _state.value = _state.value.copy(needsGoogleRegister = false)
    }

    fun logout() {
        viewModelScope.launch {
            tokenStore.clear()
            _state.value = AuthState(isLoggedIn = false)
        }
    }
}

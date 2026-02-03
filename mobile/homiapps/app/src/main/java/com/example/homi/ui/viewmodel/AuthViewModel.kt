package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.homi.data.local.TokenStore
import com.example.homi.data.repository.AuthRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.launch

data class AuthState(
    val loading: Boolean = false,
    val error: String? = null,
    val isLoggedIn: Boolean = false,
    val userName: String = "Warga"
)

class AuthViewModel(
    private val repo: AuthRepository,
    private val tokenStore: TokenStore
) : ViewModel() {

    private val _state = MutableStateFlow(AuthState())
    val state = _state.asStateFlow()

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
            _state.value = _state.value.copy(loading = true, error = null)
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
                    _state.value = _state.value.copy(
                        loading = false,
                        error = res.message ?: "Login Google gagal"
                    )
                }
            } catch (e: Exception) {
                _state.value = _state.value.copy(
                    loading = false,
                    error = e.message ?: "Login Google gagal"
                )
            }
        }
    }

    fun logout() {
        viewModelScope.launch {
            tokenStore.clear()
            _state.value = AuthState(isLoggedIn = false)
        }
    }
}

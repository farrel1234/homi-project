package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.homi.data.local.TokenStore
import com.example.homi.data.repository.AuthRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class AuthState(
    val loading: Boolean = false,
    val error: String? = null,
    val isLoggedIn: Boolean = false
)

class AuthViewModel(
    private val repo: AuthRepository,
    private val tokenStore: TokenStore
) : ViewModel() {

    private val _state = MutableStateFlow(AuthState())
    val state = _state.asStateFlow()

    fun login(email: String, password: String) {
        viewModelScope.launch {
            _state.value = AuthState(loading = true)
            try {
                val res = repo.login(email, password)
                if (res.success && res.data != null) {
                    tokenStore.saveToken(res.data.token)
                    _state.value = AuthState(isLoggedIn = true)
                } else {
                    _state.value = AuthState(error = res.message)
                }
            } catch (e: Exception) {
                _state.value = AuthState(error = e.message ?: "Error login")
            }
        }
    }
}

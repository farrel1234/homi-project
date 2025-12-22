package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import com.example.homi.data.local.TokenStore
import com.example.homi.data.remote.ApiClient
import com.example.homi.data.repository.AuthRepository

class AuthViewModelFactory(
    private val tokenStore: TokenStore
) : ViewModelProvider.Factory {

    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        if (modelClass.isAssignableFrom(AuthViewModel::class.java)) {
            val api = ApiClient.getApi(tokenStore)
            val repo = AuthRepository(api)
            @Suppress("UNCHECKED_CAST")
            return AuthViewModel(repo, tokenStore) as T
        }
        throw IllegalArgumentException("Unknown ViewModel class")
    }
}

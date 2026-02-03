package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.example.homi.data.local.TokenStore
import com.example.homi.data.model.FeeInvoiceDto
import com.example.homi.data.remote.ApiClient
import com.example.homi.data.repository.FeeRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

// Pakai tipe yang sudah ada di project kamu (di repo kamu namanya FeeInvoiceDto)

data class TagihanIuranState(
    val loading: Boolean = false,
    val invoices: List<FeeInvoiceDto> = emptyList(),
    val error: String? = null
)

class TagihanIuranViewModel(
    private val feeRepository: FeeRepository
) : ViewModel() {

    private val _state = MutableStateFlow(TagihanIuranState(loading = true))
    val state: StateFlow<TagihanIuranState> = _state.asStateFlow()

    init {
        refresh()
    }

    fun refresh() {
        viewModelScope.launch {
            _state.value = _state.value.copy(loading = true, error = null)
            try {
                val data = feeRepository.getInvoices()
                _state.value = _state.value.copy(loading = false, invoices = data)
            } catch (e: Exception) {
                _state.value = _state.value.copy(
                    loading = false,
                    error = e.message ?: "Gagal mengambil tagihan iuran"
                )
            }
        }
    }
}

class TagihanIuranViewModelFactory(
    private val tokenStore: TokenStore
) : ViewModelProvider.Factory {

    @Suppress("UNCHECKED_CAST")
    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        val api = ApiClient.getApi(tokenStore)
        val repo = FeeRepository(api)
        return TagihanIuranViewModel(repo) as T
    }
}

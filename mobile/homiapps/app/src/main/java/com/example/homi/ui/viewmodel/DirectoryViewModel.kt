package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.homi.data.model.DirectoryItem
import com.example.homi.data.repository.DirectoryRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

data class DirectoryUiState(
    val loading: Boolean = false,
    val items: List<DirectoryItem> = emptyList(),
    val error: String? = null
)

class DirectoryViewModel(
    private val repo: DirectoryRepository
) : ViewModel() {

    private val _state = MutableStateFlow(DirectoryUiState())
    val state: StateFlow<DirectoryUiState> = _state

    fun load(q: String? = null) {
        viewModelScope.launch {
            _state.value = _state.value.copy(loading = true, error = null)
            try {
                val data = repo.getDirectory(q)
                _state.value = DirectoryUiState(loading = false, items = data)
            } catch (e: Exception) {
                _state.value = DirectoryUiState(loading = false, items = emptyList(), error = e.message)
            }
        }
    }
}

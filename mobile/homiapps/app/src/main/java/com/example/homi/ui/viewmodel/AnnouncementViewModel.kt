package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.homi.data.model.AnnouncementDto
import com.example.homi.data.repository.AnnouncementRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class AnnouncementState(
    val loading: Boolean = false,
    val error: String? = null,
    val list: List<AnnouncementDto> = emptyList(),
    val detail: AnnouncementDto? = null
)

class AnnouncementViewModel(
    private val repo: AnnouncementRepository
) : ViewModel() {

    private val _state = MutableStateFlow(AnnouncementState())
    val state = _state.asStateFlow()

    fun loadList() {
        viewModelScope.launch {
            _state.value = _state.value.copy(loading = true, error = null)
            try {
                val res = repo.list()
                if (res.status) {
                    val sorted = res.data.sortedWith(
                        compareByDescending<AnnouncementDto> { it.isPinned == true }
                            .thenByDescending { it.publishedAt ?: it.createdAt ?: "" }
                            .thenByDescending { it.id } // biar stabil & biasanya paling baru
                    )

                    _state.value = _state.value.copy(
                        loading = false,
                        list = sorted
                    )
                } else {
                    _state.value = _state.value.copy(
                        loading = false,
                        error = "Gagal memuat pengumuman"
                    )
                }
            } catch (e: Exception) {
                _state.value = _state.value.copy(
                    loading = false,
                    error = e.message
                )
            }
        }
    }

    fun loadDetail(id: Long) {
        viewModelScope.launch {
            _state.value = _state.value.copy(loading = true, error = null)
            try {
                val res = repo.detail(id)
                if (res.status) {
                    _state.value = _state.value.copy(
                        loading = false,
                        detail = res.data
                    )
                } else {
                    _state.value = _state.value.copy(
                        loading = false,
                        error = "Detail tidak ditemukan"
                    )
                }
            } catch (e: Exception) {
                _state.value = _state.value.copy(
                    loading = false,
                    error = e.message
                )
            }
        }
    }
}

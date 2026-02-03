package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.homi.data.repository.NotificationRepository
import kotlinx.coroutines.launch
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update

class NotificationViewModel(
    private val repo: NotificationRepository
) : ViewModel() {

    private val _state = MutableStateFlow(NotificationUiState())
    val state: StateFlow<NotificationUiState> = _state.asStateFlow()

    /** Load list + unread count */
    fun refresh(page: Int = 1) = viewModelScope.launch {
        _state.update { it.copy(loading = true, error = null) }

        runCatching {
            val list = repo.list(page)
            val unread = repo.unreadCount()
            list to unread
        }.onSuccess { (list, unread) ->
            _state.update {
                it.copy(
                    loading = false,
                    items = list.data,
                    unreadCount = unread,
                    currentPage = list.currentPage,
                    lastPage = list.lastPage,
                    error = null
                )
            }
        }.onFailure { e ->
            _state.update { it.copy(loading = false, error = e.message ?: "Gagal memuat notifikasi") }
        }
    }

    /** Khusus refresh angka badge */
    fun refreshUnreadCount() = viewModelScope.launch {
        runCatching { repo.unreadCount() }
            .onSuccess { unread ->
                _state.update { it.copy(unreadCount = unread) }
            }
            .onFailure { /* biarin, badge jangan ganggu UI */ }
    }

    /** Alias biar kompatibel sama kode lama yang sempat kamu pakai */
    fun load() = refresh()
    fun refreshUnreadOnly() = refreshUnreadCount()

    fun markRead(id: Long) = viewModelScope.launch {
        // kalau sudah read, gak usah hit server lagi
        val alreadyRead = _state.value.items.firstOrNull { it.id == id }?.isRead == true
        if (alreadyRead) return@launch

        runCatching { repo.markRead(id) }
            .onSuccess {
                _state.update { s ->
                    s.copy(
                        items = s.items.map { if (it.id == id) it.copy(isRead = true) else it },
                        unreadCount = (s.unreadCount - 1).coerceAtLeast(0)
                    )
                }
            }
            .onFailure { e ->
                _state.update { it.copy(error = e.message ?: "Gagal menandai dibaca") }
            }
    }

    fun readAll() = viewModelScope.launch {
        runCatching { repo.readAll() }
            .onSuccess {
                _state.update { s ->
                    s.copy(
                        items = s.items.map { it.copy(isRead = true) },
                        unreadCount = 0
                    )
                }
            }
            .onFailure { e ->
                _state.update { it.copy(error = e.message ?: "Gagal baca semua") }
            }
    }
}

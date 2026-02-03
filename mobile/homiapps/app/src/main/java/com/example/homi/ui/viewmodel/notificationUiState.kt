package com.example.homi.ui.viewmodel

import com.example.homi.data.model.NotificationItem

data class NotificationUiState(
    val loading: Boolean = false,
    val error: String? = null,
    val items: List<NotificationItem> = emptyList(),
    val unreadCount: Int = 0,
    val currentPage: Int = 1,
    val lastPage: Int = 1
)

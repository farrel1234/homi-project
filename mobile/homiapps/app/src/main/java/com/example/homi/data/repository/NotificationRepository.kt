package com.example.homi.data.repository

import com.example.homi.data.model.BasicMessageResponse
import com.example.homi.data.model.NotificationListResponse
import com.example.homi.data.remote.ApiService

class NotificationRepository(
    private val api: ApiService
) {
    suspend fun list(page: Int = 1): NotificationListResponse {
        return api.getNotifications(page)
    }

    suspend fun unreadCount(): Int {
        return api.getNotificationUnreadCount().unreadCount
    }

    suspend fun markRead(id: Long): BasicMessageResponse {
        return api.markNotificationRead(id)
    }

    suspend fun readAll(): BasicMessageResponse {
        return api.readAllNotifications()
    }
}

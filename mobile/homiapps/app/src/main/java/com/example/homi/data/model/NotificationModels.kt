package com.example.homi.data.model

import com.google.gson.annotations.SerializedName

data class NotificationItem(
    val id: Long,
    val type: String? = null,
    val title: String = "",
    val body: String? = null,
    val meta: Map<String, Any?>? = null,
    @SerializedName("created_at") val createdAt: String? = null,
    @SerializedName("is_read") val isRead: Boolean = false
)

data class NotificationListResponse(
    val data: List<NotificationItem> = emptyList(),
    @SerializedName("current_page") val currentPage: Int = 1,
    @SerializedName("last_page") val lastPage: Int = 1,
    @SerializedName("per_page") val perPage: Int = 20,
    val total: Int = 0
)

data class UnreadCountResponse(
    @SerializedName("unread_count") val unreadCount: Int = 0
)

data class BasicMessageResponse(
    val ok: Boolean? = null,
    val message: String? = null
)

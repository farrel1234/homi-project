package com.example.homi.data.model

import com.google.gson.annotations.SerializedName

data class AnnouncementsResponse(
    val status: Boolean,
    val data: List<AnnouncementDto>
)

data class AnnouncementDetailResponse(
    val status: Boolean,
    val data: AnnouncementDto
)

data class AnnouncementDto(
    val id: Long,
    val title: String,
    val content: String,

    @SerializedName("image_path")
    val imagePath: String? = null,

    @SerializedName("image_url")
    val imageUrl: String? = null,

    @SerializedName("published_at")
    val publishedAt: String? = null,

    @SerializedName("created_by")
    val createdBy: Long? = null,

    @SerializedName("is_pinned")
    val isPinned: Boolean? = null,

    @SerializedName("created_at")
    val createdAt: String? = null,

    @SerializedName("updated_at")
    val updatedAt: String? = null,
)

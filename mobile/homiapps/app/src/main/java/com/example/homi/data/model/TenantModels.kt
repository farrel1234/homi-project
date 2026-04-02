package com.example.homi.data.model

import com.google.gson.annotations.SerializedName

data class TenantListResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("data") val data: List<TenantListData>,
    @SerializedName("message") val message: String?
)

data class TenantListData(
    @SerializedName("name") val name: String,
    @SerializedName("code") val code: String
)

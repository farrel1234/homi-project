package com.example.homi.data.remote

import com.example.homi.data.model.*
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path

interface ApiService {
    @POST("login")
    suspend fun login(@Body req: LoginRequest): LoginResponse

    // contoh: GET /api/announcements
    @GET("announcements")
    suspend fun getAnnouncements(): AnnouncementsResponse

    // contoh: GET /api/announcements/{id}
    @GET("announcements/{id}")
    suspend fun getAnnouncementDetail(@Path("id") id: Long): AnnouncementDetailResponse

}

    package com.example.homi.data.repository

    import com.example.homi.data.remote.ApiService

    class AnnouncementRepository(private val api: ApiService) {
        suspend fun list() = api.getAnnouncements()
        suspend fun detail(id: Long) = api.getAnnouncementDetail(id)
    }

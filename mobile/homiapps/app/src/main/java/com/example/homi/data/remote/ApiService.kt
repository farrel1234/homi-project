package com.example.homi.data.remote

import com.example.homi.data.model.*
import com.example.homi.data.model.CreateServiceRequestBody
import okhttp3.MultipartBody
import okhttp3.RequestBody
import okhttp3.ResponseBody
import retrofit2.Response
import retrofit2.http.*
import com.example.homi.data.model.NotificationListResponse
import com.example.homi.data.model.UnreadCountResponse
import com.example.homi.data.model.BasicMessageResponse
import com.example.homi.data.model.OkResponse
import com.example.homi.data.model.VerifyOtpData
import com.example.homi.data.model.RegisterData
import com.example.homi.data.model.LoginRequest
import com.example.homi.data.model.RegisterRequest
import com.example.homi.data.model.VerifyOtpRequest

interface ApiService {
    @POST("login")
    suspend fun login(@Body body: LoginRequest): ApiResponse<VerifyOtpData>

    @POST("register")
    suspend fun register(@Body body: RegisterRequest): ApiResponse<RegisterData>

    @GET("tenants")
    suspend fun getPublicTenants(): TenantListResponse

    @POST("verify-otp")
    suspend fun verifyOtp(@Body body: VerifyOtpRequest): ApiResponse<VerifyOtpData>

    @POST("resend-otp")
    suspend fun resendOtp(@Body body: ForgotPasswordRequest): ApiResponse<ForgotPasswordData>

    @POST("forgot-password")
    suspend fun forgotPassword(@Body body: ForgotPasswordRequest): ApiResponse<ForgotPasswordData>

    @POST("verify-reset-otp")
    suspend fun verifyResetOtp(@Body body: VerifyOtpRequest): ApiResponse<VerifyResetOtpData>

    @POST("reset-password")
    suspend fun resetPassword(@Body body: ResetPasswordRequest): ApiResponse<Any>

    // GOOGLE LOGIN
    @POST("login-google")
    suspend fun loginGoogle(@Body body: GoogleLoginRequest): ApiResponse<VerifyOtpData>

    @POST("login-google")
    suspend fun loginGoogleRaw(@Body body: GoogleLoginRequest): Response<ResponseBody>

    // ANNOUNCEMENTS
    @GET("announcements")
    suspend fun getAnnouncements(): AnnouncementsResponse

    @GET("announcements/{id}")
    suspend fun getAnnouncementDetail(@Path("id") id: Long): AnnouncementDetailResponse

    // DIRECTORY
    @GET("directory")
    suspend fun getDirectory(@Query("q") q: String? = null): DirectoryResponse

    // COMPLAINTS
    @GET("complaints")
    suspend fun getComplaints(): ComplaintsResponse

    @GET("complaints/{id}")
    suspend fun getComplaintDetail(@Path("id") id: Long): ComplaintDetailResponse

    @Multipart
    @POST("complaints")
    suspend fun createComplaint(
        @Part("nama_pelapor") namaPelapor: RequestBody,
        @Part("tanggal_pengaduan") tanggalPengaduan: RequestBody,
        @Part("tempat_kejadian") tempatKejadian: RequestBody,
        @Part("perihal") perihal: RequestBody,
        @Part foto: MultipartBody.Part?
    ): CreateComplaintResponse

    // SERVICE REQUESTS
    @GET("request-types")
    suspend fun getRequestTypes(): RequestTypesResponse

    @POST("service-requests")
    suspend fun createServiceRequest(@Body body: CreateServiceRequestBody): CreateServiceRequestResponse

    @GET("service-requests")
    suspend fun getServiceRequests(): ServiceRequestsResponse

    @GET("service-requests/{id}")
    suspend fun getServiceRequestDetail(@Path("id") id: Long): ServiceRequestDetailResponse

    @Streaming
    @GET("service-requests/{id}/download")
    suspend fun downloadServiceRequestPdf(@Path("id") id: Long): Response<ResponseBody>

    // FEES
    @GET("fees/invoices")
    suspend fun getFeeInvoices(): FeeInvoicesResponse

    @GET("payment-qr-codes/active")
    suspend fun getActiveFeeQr(): ActiveQrResponse

    @Multipart
    @POST("fees/invoices/{invoiceId}/pay")
    suspend fun payInvoice(
        @Path("invoiceId") invoiceId: Long,
        @Part proofImage: MultipartBody.Part,
        @Part("note") note: RequestBody? = null
    ): PayInvoiceResponse

    // NOTIFICATIONS
    @GET("notifications")
    suspend fun getNotifications(@Query("page") page: Int = 1): NotificationListResponse

    @GET("notifications/unread-count")
    suspend fun getNotificationUnreadCount(): UnreadCountResponse

    @POST("notifications/{id}/read")
    suspend fun markNotificationRead(@Path("id") id: Long): BasicMessageResponse

    @POST("notifications/read-all")
    suspend fun readAllNotifications(): BasicMessageResponse

    // PROFILE
    @POST("change-password")
    suspend fun changePassword(@Body req: ChangePasswordRequest): OkResponse

    @PUT("me/resident-profile")
    suspend fun upsertResidentProfileMap(@Body body: Map<String, String>): Response<OkResponse>

    @GET("me/resident-profile")
    suspend fun getMyResidentProfileRaw(): Response<ResponseBody>

    @PUT("me")
    suspend fun updateMyProfile(@Body body: Map<String, String>): Response<ResponseBody>

    @GET("me")
    suspend fun getMe(): Response<ResponseBody>

    @POST("me/fcm-token")
    suspend fun updateFcmToken(@Body body: Map<String, String>): Response<ResponseBody>

    @Multipart
    @POST("me/photo")
    suspend fun updateProfilePhoto(
        @Part photo: MultipartBody.Part
    ): Response<ResponseBody>
}

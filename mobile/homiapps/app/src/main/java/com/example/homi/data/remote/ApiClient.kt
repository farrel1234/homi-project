package com.example.homi.data.remote

import android.util.Log
import com.example.homi.data.local.TokenStore
import kotlinx.coroutines.flow.first
import okhttp3.Interceptor
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

object ApiClient {

    @Volatile
    private var retrofit: Retrofit? = null

    fun getApi(tokenStore: TokenStore): ApiService {
        val instance = retrofit
        if (instance != null) return instance.create(ApiService::class.java)

        synchronized(this) {
            val again = retrofit
            if (again != null) return again.create(ApiService::class.java)

            // 1) Logging
            val logging = HttpLoggingInterceptor().apply {
                level = HttpLoggingInterceptor.Level.BODY
            }

            // 2) Debug interceptor: cek contentType request body (penting buat kasus multipart)
            val debugContentTypeInterceptor = Interceptor { chain ->
                val req = chain.request()
                val ct = req.body?.contentType()
                val len = try { req.body?.contentLength() } catch (e: Exception) { -1L }

                Log.d("UPLOAD_DEBUG", "REQ ${req.method} ${req.url}")
                Log.d("UPLOAD_DEBUG", "body.contentType=$ct contentLength=$len")

                chain.proceed(req)
            }

            // 3) Biar Laravel selalu balikin JSON
            val acceptJsonInterceptor = Interceptor { chain ->
                val builder = chain.request().newBuilder()
                    .header("Accept", "application/json")
                val tenantCode = ApiConfig.tenantCode.trim()
                if (tenantCode.isNotEmpty()) {
                    builder.header(ApiConfig.TENANT_HEADER, tenantCode)
                }

                val req = builder.build()
                chain.proceed(req)
            }

            val okHttp = OkHttpClient.Builder()
                // logging
                .addInterceptor(logging)

                // debug content-type
                .addInterceptor(debugContentTypeInterceptor)

                // Accept JSON
                .addInterceptor(acceptJsonInterceptor)

                // Authorization
                .addInterceptor(
                    AuthInterceptor {
                        // runBlocking TIDAK perlu karena getApi bukan suspend,
                        // tapi tokenFlow.first() itu suspend -> maka kita ambil lewat kotlinx.coroutines.runBlocking
                        // biar tetap sync dan simple (sesuai pattern kamu).
                        kotlinx.coroutines.runBlocking { tokenStore.tokenFlow.first() }
                    }
                )
                .connectTimeout(30, TimeUnit.SECONDS)
                .readTimeout(30, TimeUnit.SECONDS)
                .writeTimeout(30, TimeUnit.SECONDS)
                .build()

            val built = Retrofit.Builder()
                .baseUrl(ApiConfig.BASE_URL) // contoh: "http://192.168.1.2:8000/api/"
                .client(okHttp)
                .addConverterFactory(GsonConverterFactory.create())
                .build()

            retrofit = built
            return built.create(ApiService::class.java)
        }
    }

    fun clear() {
        retrofit = null
    }

    fun getApiMock(): ApiService {
        return Retrofit.Builder()
            .baseUrl("http://localhost/")
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiService::class.java)
    }
}

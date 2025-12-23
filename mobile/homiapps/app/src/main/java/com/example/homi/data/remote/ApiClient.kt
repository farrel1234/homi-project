package com.example.homi.data.remote

import com.example.homi.data.local.TokenStore
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.runBlocking
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

object ApiClient {

    private var retrofit: Retrofit? = null

    fun getApi(tokenStore: TokenStore): ApiService {
        if (retrofit == null) {

            val logging = HttpLoggingInterceptor().apply {
                level = HttpLoggingInterceptor.Level.BODY
            }

            val okHttp = OkHttpClient.Builder()
                .addInterceptor(logging)
                .addInterceptor(
                    AuthInterceptor {
                        runBlocking { tokenStore.tokenFlow.first() } // ambil token dari DataStore
                    }
                )
                .build()

            retrofit = Retrofit.Builder()
                .baseUrl(ApiConfig.BASE_URL)
                .client(okHttp)
                .addConverterFactory(GsonConverterFactory.create())
                .build()
        }

        return retrofit!!.create(ApiService::class.java)
    }
}

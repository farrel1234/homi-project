package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.lazy.rememberLazyListState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Notifications
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import com.example.homi.R
import com.example.homi.data.local.TokenStore
import com.example.homi.data.model.AnnouncementDto
import com.example.homi.data.model.DirectoryItem
import com.example.homi.data.repository.ComplaintRepository
import com.example.homi.data.repository.ServiceRequestRepository
import com.example.homi.ui.viewmodel.AnnouncementViewModel
import com.example.homi.ui.viewmodel.DirectoryViewModel
import com.example.homi.ui.viewmodel.NotificationUiState
import com.example.homi.ui.viewmodel.NotificationViewModel
import kotlinx.coroutines.flow.flow
import androidx.compose.material3.HorizontalDivider

/* ===== Tokens ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val BlueButton   = Color(0xFF4F8EA9)
private val AccentOrange = Color(0xFFE26A2C)

private val BlueBorder   = Color(0xFF2F7FA3)
private val TextPrimary  = Color(0xFF0E0E0E)
private val LineGray     = Color(0xFFE6E6E6)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))
private val SuezOne     = FontFamily(Font(R.font.suez_one_regular))

private enum class BottomTab { BERANDA, DIREKTORI, RIWAYAT, AKUN }

private data class BottomNavItem(
    val tab: BottomTab,
    val label: String,
    @DrawableRes val iconSelected: Int,
    @DrawableRes val iconUnselected: Int
)

@Composable
fun DashboardScreen(
    annVm: AnnouncementViewModel,
    tokenStore: TokenStore,

    notifVm: NotificationViewModel? = null,
    onNotifications: (() -> Unit)? = null,

    dirVm: DirectoryViewModel? = null,

    serviceRepo: ServiceRequestRepository? = null,
    complaintRepo: ComplaintRepository? = null,
    refreshRiwayatKey: Boolean = false,

    onPengajuan: (() -> Unit)? = null,
    onPengaduan: (() -> Unit)? = null,
    onPembayaran: (() -> Unit)? = null,
    onDetailPengumumanClicked: ((Long) -> Unit)? = null,

    onOpenPengaduanStepper: ((Long) -> Unit)? = null,
    onOpenSuratStatus: ((Long) -> Unit)? = null,

    onUbahKataSandi: (() -> Unit)? = null,
    onLaporkanMasalah: (() -> Unit)? = null,
    onKeluarConfirmed: (() -> Unit)? = null,
    onProsesPengajuan: (() -> Unit)? = null,
) {
    var currentTab by rememberSaveable { mutableStateOf(BottomTab.BERANDA) }

    val nameFlow = runCatching { tokenStore.nameFlow }.getOrNull() ?: flow { emit("Warga") }
    val savedName by nameFlow.collectAsState(initial = "Warga")
    val displayName = savedName?.trim().takeIf { !it.isNullOrBlank() } ?: "Warga"

    val annState by annVm.state.collectAsState()

    val notifState: NotificationUiState? =
        notifVm?.state?.collectAsState(initial = NotificationUiState())?.value
    val unreadCount = notifState?.unreadCount ?: 0

    LaunchedEffect(Unit) { annVm.loadList() }

    // refresh badge saat masuk tab BERANDA
    LaunchedEffect(currentTab) {
        if (currentTab == BottomTab.BERANDA) {
            notifVm?.refreshUnreadOnly()
        }
    }

    val latest = annState.list.firstOrNull()

    Column(modifier = Modifier.fillMaxSize()) {
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .weight(1f)
        ) {
            when (currentTab) {
                BottomTab.BERANDA -> BerandaSection(
                    item = latest,
                    userName = displayName,
                    unreadCount = unreadCount,
                    onNotifications = onNotifications,
                    onPengajuan = onPengajuan,
                    onPengaduan = onPengaduan,
                    onPembayaran = onPembayaran,
                    onDetailPengumumanClicked = onDetailPengumumanClicked
                )

                BottomTab.DIREKTORI -> {
                    if (dirVm == null) {
                        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Text("Direktori belum dihubungkan (dirVm null)", fontFamily = PoppinsSemi)
                        }
                    } else {
                        DirektoriSection(dirVm = dirVm)
                    }
                }

                BottomTab.RIWAYAT -> {
                    if (serviceRepo == null || complaintRepo == null) {
                        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Text("Repo riwayat belum siap", fontFamily = PoppinsSemi)
                        }
                    } else {
                        key(refreshRiwayatKey) {
                            Riwayat1Screen(
                                serviceRepo = serviceRepo,
                                complaintRepo = complaintRepo,
                                onPengaduanItemClick = { id -> onOpenPengaduanStepper?.invoke(id) },
                                onPengajuanSuratClick = { id -> onOpenSuratStatus?.invoke(id) }
                            )
                        }
                    }
                }

                BottomTab.AKUN -> AkunScreen(
                    tokenStore = tokenStore,
                    onUbahKataSandi = onUbahKataSandi,
                    onProsesPengajuan = onProsesPengajuan,
                    onLaporkanMasalah = onLaporkanMasalah,
                    onKeluarConfirmed = { onKeluarConfirmed?.invoke() }
                )
            }
        }

        BottomNavBar(
            currentTab = currentTab,
            onTabSelected = { selected -> currentTab = selected }
        )
    }
}

@Composable
private fun BerandaSection(
    item: AnnouncementDto?,
    userName: String,
    unreadCount: Int,
    onNotifications: (() -> Unit)?,
    onPengajuan: (() -> Unit)?,
    onPengaduan: (() -> Unit)?,
    onPembayaran: (() -> Unit)?,
    onDetailPengumumanClicked: ((Long) -> Unit)?
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 20.dp, vertical = 14.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Image(
                painter = painterResource(R.drawable.icon_profile),
                contentDescription = "Profil",
                modifier = Modifier.size(80.dp).clip(CircleShape)
            )

            Spacer(Modifier.width(12.dp))

            Column(modifier = Modifier.weight(1f)) {
                Text("Hai, $userName", fontFamily = PoppinsSemi, fontSize = 20.sp, color = Color.White)
                Text("Selamat Datang di Homi", fontFamily = PoppinsSemi, fontSize = 20.sp, color = Color.White)
                Text(
                    "Menghubungkan Warga, Membangun Kebersamaan",
                    fontFamily = PoppinsReg, fontSize = 12.sp, color = Color.White
                )
            }

            NotificationBell(
                unreadCount = unreadCount,
                onClick = onNotifications
            )
        }

        Spacer(Modifier.height(10.dp))

        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            val listState = rememberLazyListState()

            LazyColumn(
                state = listState,
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 16.dp, vertical = 18.dp),
                verticalArrangement = Arrangement.spacedBy(14.dp),
                contentPadding = PaddingValues(bottom = 24.dp)
            ) {
                item {
                    Text(
                        text = "Pengumuman",
                        fontFamily = PoppinsSemi,
                        fontSize = 20.sp,
                        color = AccentOrange,
                        modifier = Modifier.fillMaxWidth(),
                        textAlign = TextAlign.Center
                    )
                }

                item {
                    val imageUrl = item?.imageUrl
                        ?.replace("127.0.0.1", "10.0.2.2")
                        ?.replace("localhost", "10.0.2.2")

                    // ✅ PERUBAHAN: klik gambar/banner langsung ke detail
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(220.dp)
                            .clip(RoundedCornerShape(16.dp))
                            .clickable(
                                enabled = item != null && onDetailPengumumanClicked != null
                            ) { onDetailPengumumanClicked?.invoke(item!!.id) }
                    ) {
                        if (!imageUrl.isNullOrBlank()) {
                            AsyncImage(
                                model = imageUrl,
                                contentDescription = item?.title ?: "Pengumuman",
                                contentScale = ContentScale.Crop,
                                modifier = Modifier.fillMaxSize()
                            )
                        } else {
                            Image(
                                painter = painterResource(R.drawable.img_pengumuman),
                                contentDescription = "Pengumuman",
                                contentScale = ContentScale.Crop,
                                modifier = Modifier.fillMaxSize()
                            )
                        }

                        Box(
                            modifier = Modifier
                                .matchParentSize()
                                .background(Color.Black.copy(alpha = 0.30f))
                        )

                        // ✅ Judul tidak wajib diklik lagi (hapus clickable di judul)
                        Text(
                            text = item?.title ?: "Pengumuman terbaru belum tersedia",
                            fontFamily = SuezOne,
                            color = Color.White,
                            fontSize = 18.sp,
                            textAlign = TextAlign.Center,
                            modifier = Modifier
                                .align(Alignment.TopCenter)
                                .padding(top = 10.dp)
                                .fillMaxWidth()
                        )

                        val dateText = item?.publishedAt ?: item?.createdAt ?: ""
                        if (dateText.isNotBlank()) {
                            Text(
                                text = dateText,
                                fontFamily = PoppinsReg,
                                fontSize = 12.sp,
                                color = Color.White,
                                modifier = Modifier
                                    .align(Alignment.BottomStart)
                                    .padding(12.dp)
                            )
                        }
                    }
                }

                item {
                    MenuButtonSymmetric(
                        icon = R.drawable.icon_pengajuan,
                        title = "Pengajuan Layanan",
                        onClick = onPengajuan
                    )
                }
                item {
                    MenuButtonSymmetric(
                        icon = R.drawable.icon_pengaduan,
                        title = "Pengaduan Warga",
                        onClick = onPengaduan
                    )
                }
                item {
                    MenuButtonSymmetric(
                        icon = R.drawable.icon_pembayaran,
                        title = "Pembayaran Iuran",
                        onClick = onPembayaran
                    )
                }

            }
        }
    }
}

@Composable
private fun NotificationBell(
    unreadCount: Int,
    onClick: (() -> Unit)? = null
) {
    BadgedBox(
        badge = {
            if (unreadCount > 0) {
                Badge(
                    containerColor = AccentOrange,
                    contentColor = Color.White
                ) {
                    Text(
                        text = if (unreadCount > 99) "99+" else unreadCount.toString(),
                        fontFamily = PoppinsSemi,
                        fontSize = 10.sp
                    )
                }
            }
        }
    ) {
        IconButton(
            onClick = { onClick?.invoke() },
            enabled = onClick != null
        ) {
            Icon(
                imageVector = Icons.Default.Notifications,
                contentDescription = "Notifikasi",
                tint = Color.White
            )
        }
    }
}

@Composable
private fun MenuButtonSymmetric(
    @DrawableRes icon: Int,
    title: String,
    onClick: (() -> Unit)? = null,
) {
    val shape = RoundedCornerShape(16.dp)

    // ✅ ukuran FIX biar sejajar
    val iconBox = 56.dp

    // ✅ inner icon size: “visual sama besar” (beda tipis, bukan 70dp)
    val iconSize = when (icon) {
        R.drawable.icon_pengajuan -> 34.dp
        R.drawable.icon_pengaduan -> 38.dp
        R.drawable.icon_pembayaran -> 38.dp
        else -> 34.dp
    }

    Row(
        modifier = Modifier
            .fillMaxWidth()
            .height(76.dp) // sedikit lebih tinggi biar lega
            .clip(shape)
            .background(BlueButton)
            .clickable(enabled = onClick != null) { onClick?.invoke() }
            .padding(horizontal = 16.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        // ✅ kotak icon seragam → otomatis rata
        Box(
            modifier = Modifier
                .size(iconBox)
                .clip(RoundedCornerShape(14.dp))
                .background(Color.White.copy(alpha = 0.14f)),
            contentAlignment = Alignment.Center
        ) {
            Image(
                painter = painterResource(icon),
                contentDescription = title,
                modifier = Modifier.size(iconSize),
                contentScale = ContentScale.Fit
            )
        }

        Spacer(Modifier.width(14.dp))

        Text(
            text = title,
            fontFamily = PoppinsSemi,
            color = Color.White,
            fontSize = 16.sp,
            maxLines = 1
        )
    }
}


/* ===== DIREKTORI SECTION (tetap seperti punyamu) ===== */
@Composable
private fun DirektoriSection(dirVm: DirectoryViewModel) {
    val state by dirVm.state.collectAsState()
    var q by rememberSaveable { mutableStateOf("") }

    LaunchedEffect(Unit) {
        if (state.items.isEmpty() && !state.loading) dirVm.load(null)
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        Spacer(Modifier.height(8.dp))

        Text(
            text = "Direktori",
            fontFamily = PoppinsSemi,
            fontSize = 22.sp,
            color = Color.White,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp),
            textAlign = TextAlign.Center
        )

        Spacer(Modifier.height(6.dp))

        Text(
            text = "Berikut adalah nama dan alamat warga perumahan\nHawaii Garden",
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = Color.White,
            lineHeight = 18.sp,
            textAlign = TextAlign.Center,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 24.dp)
        )

        Spacer(Modifier.height(18.dp))

        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(modifier = Modifier.fillMaxSize().padding(16.dp)) {
                OutlinedTextField(
                    value = q,
                    onValueChange = { q = it },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    textStyle = LocalTextStyle.current.copy(fontFamily = PoppinsReg, fontSize = 14.sp),
                    placeholder = { Text("Cari nama / blok...", fontFamily = PoppinsReg) },
                    trailingIcon = {
                        Text(
                            text = "Cari",
                            fontFamily = PoppinsSemi,
                            color = BlueBorder,
                            modifier = Modifier
                                .padding(end = 12.dp)
                                .clickable { dirVm.load(q.trim().ifBlank { null }) }
                        )
                    },
                    shape = RoundedCornerShape(18.dp),
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = BlueBorder,
                        unfocusedBorderColor = BlueBorder
                    )
                )

                Spacer(Modifier.height(12.dp))

                if (state.loading) {
                    Box(
                        Modifier
                            .fillMaxWidth()
                            .padding(vertical = 10.dp),
                        contentAlignment = Alignment.Center
                    ) {
                        CircularProgressIndicator()
                    }
                }

                state.error?.let { err ->
                    Text(text = err, fontFamily = PoppinsReg, color = Color.Red, fontSize = 12.sp)
                    Spacer(Modifier.height(8.dp))
                }

                DirektoriTable(items = state.items)
            }
        }
    }
}

@Composable
private fun DirektoriTable(items: List<DirectoryItem>) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(8.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, LineGray, RoundedCornerShape(8.dp))
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(AccentOrange)
                    .clip(RoundedCornerShape(topStart = 8.dp, topEnd = 8.dp))
                    .height(IntrinsicSize.Min)
                    .padding(vertical = 10.dp, horizontal = 12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    "Nama",
                    fontFamily = PoppinsSemi,
                    color = Color.White,
                    fontSize = 14.sp,
                    modifier = Modifier.weight(1f)
                )
                Box(Modifier.fillMaxHeight().width(1.dp).background(Color.White.copy(alpha = 0.6f)))
                Text(
                    "Blok Alamat",
                    fontFamily = PoppinsSemi,
                    color = Color.White,
                    fontSize = 14.sp,
                    modifier = Modifier
                        .weight(1f)
                        .padding(start = 12.dp)
                )
            }

            if (items.isEmpty()) {
                Box(Modifier.fillMaxWidth().padding(16.dp), contentAlignment = Alignment.Center) {
                    Text("Data direktori kosong", fontFamily = PoppinsReg, color = Color.Gray)
                }
            } else {
                LazyColumn {
                    items(items) {
                        TableRowDirectory(it)
                        Box(Modifier.fillMaxWidth().height(1.dp).background(LineGray))
                    }
                }
            }
        }
    }
}

@Composable
private fun TableRowDirectory(item: DirectoryItem) {
    val alamat = item.blokAlamat ?: listOf(item.blok?.trim(), item.noRumah?.trim())
        .filter { !it.isNullOrBlank() }
        .joinToString(" ")
        .ifBlank { "-" }

    Row(
        modifier = Modifier
            .fillMaxWidth()
            .heightIn(min = 52.dp)
            .padding(horizontal = 12.dp, vertical = 10.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = item.name,
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = TextPrimary,
            modifier = Modifier.weight(1f),
            maxLines = 2,
            overflow = TextOverflow.Ellipsis
        )
        Box(Modifier.fillMaxHeight().width(1.dp).background(LineGray))
        Text(
            text = alamat,
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = TextPrimary,
            modifier = Modifier
                .weight(1f)
                .padding(start = 12.dp),
            maxLines = 2,
            overflow = TextOverflow.Ellipsis
        )
    }
}

@Composable
private fun BottomNavBar(
    currentTab: BottomTab,
    onTabSelected: (BottomTab) -> Unit
) {
    val items = listOf(
        BottomNavItem(BottomTab.BERANDA, "Beranda", R.drawable.homeoren, R.drawable.icon_home),
        BottomNavItem(BottomTab.DIREKTORI, "Direktori", R.drawable.direktorioren, R.drawable.icon_direktori),
        BottomNavItem(BottomTab.RIWAYAT, "Riwayat", R.drawable.riwayatoren, R.drawable.icon_riwayat),
        BottomNavItem(BottomTab.AKUN, "Akun", R.drawable.akunoren, R.drawable.icon_akun),
    )

    Surface(color = Color(0xFFF6F6F6), tonalElevation = 8.dp, shadowElevation = 8.dp) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 10.dp, vertical = 6.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            items.forEach { item ->
                val selected = item.tab == currentTab
                val iconId = if (selected) item.iconSelected else item.iconUnselected
                val labelColor = if (selected) Color.White else Color(0xFF6C6C6C)

                Column(
                    modifier = Modifier
                        .weight(1f)
                        .clickable { onTabSelected(item.tab) },
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Box(
                        modifier = Modifier
                            .clip(RoundedCornerShape(50))
                            .background(if (selected) AccentOrange else Color.Transparent)
                            .padding(horizontal = 14.dp, vertical = 6.dp),
                        contentAlignment = Alignment.Center
                    ) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Image(
                                painter = painterResource(id = iconId),
                                contentDescription = item.label,
                                modifier = Modifier.size(20.dp),
                                contentScale = ContentScale.Fit
                            )
                            Spacer(Modifier.height(2.dp))
                            Text(
                                item.label,
                                fontFamily = PoppinsReg,
                                fontSize = 10.sp,
                                color = labelColor,
                                maxLines = 1
                            )
                        }
                    }
                }
            }
        }
    }
}

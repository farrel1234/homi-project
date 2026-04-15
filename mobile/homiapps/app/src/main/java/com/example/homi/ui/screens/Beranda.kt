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
import androidx.compose.ui.draw.rotate
import coil.compose.AsyncImage
import coil.compose.AsyncImagePainter
import coil.compose.rememberAsyncImagePainter
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
import com.example.homi.util.DateUtils
import androidx.compose.material3.HorizontalDivider

/* ===== Tokens ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val BlueDark     = Color(0xFF1A5E7B)
private val BlueButton   = Color(0xFF4F8EA9)
private val AccentOrange = Color(0xFFF7A477)

private val BlueBorder   = Color(0xFF2F7FA3)
private val TextPrimary  = Color(0xFF0E0E0E)
private val LineGray     = Color(0xFFE6E6E6)
private val HintGray     = Color(0xFF8A8A8A)

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

    onPengajuanLayanan: (() -> Unit)? = null,
    onPengajuanSurat: (() -> Unit)? = null,
    onPengaduan: (() -> Unit)? = null,
    onPembayaran: (() -> Unit)? = null,
    onDetailPengumumanClicked: ((Long) -> Unit)? = null,

    onOpenSuratStatus: ((Long) -> Unit)? = null,

    onUbahKataSandi: (() -> Unit)? = null,
    onEditProfil: (() -> Unit)? = null,
    onProsesPengajuan: ((Long) -> Unit)? = null,
    onLaporkanMasalah: (() -> Unit)? = null,
    onKeluarConfirmed: (() -> Unit)? = null,
    onDetailRiwayatPengaduan: ((Long) -> Unit)? = null,
    onDetailRiwayatPengajuan: ((Long) -> Unit)? = null,
) {
    var currentTab by rememberSaveable { mutableStateOf(BottomTab.BERANDA) }

    val nameFlow = runCatching { tokenStore.nameFlow }.getOrNull() ?: flow { emit("Warga") }
    val savedName by nameFlow.collectAsState(initial = "Warga")
    val displayName = savedName?.trim().takeIf { !it.isNullOrBlank() } ?: "Warga"

    val nikFlow = runCatching { tokenStore.nikFlow }.getOrNull() ?: flow { emit("") }
    val savedNik by nikFlow.collectAsState(initial = "")
    val displayNik = savedNik?.trim().takeIf { !it.isNullOrBlank() } ?: "NIK belum tersedia"

    val tenantNameFlow = runCatching { tokenStore.tenantNameFlow }.getOrNull() ?: flow { emit("Homi Garden") }
    val savedTenantName by tenantNameFlow.collectAsState(initial = "Homi Garden")
    val displayTenantName = savedTenantName?.trim().takeIf { !it.isNullOrBlank() } ?: "Homi Garden"

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

    Column(
        modifier = Modifier
            .fillMaxSize()
            .navigationBarsPadding()
    ) {
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .weight(1f)
        ) {
            when (currentTab) {
                BottomTab.BERANDA -> BerandaSection(
                    item = latest,
                    userName = displayName,
                    tenantName = displayTenantName,
                    unreadCount = unreadCount,
                    onNotifications = onNotifications,
                    onPengajuanLayanan = onPengajuanLayanan,
                    onPengajuanSurat = onPengajuanSurat,
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
                        DirektoriSection(dirVm = dirVm, tenantName = displayTenantName)
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
                                onPengaduanItemClick = { id -> onDetailRiwayatPengaduan?.invoke(id) ?: onProsesPengajuan?.invoke(id) },
                                onPengajuanSuratClick = { id -> onDetailRiwayatPengajuan?.invoke(id) ?: onOpenSuratStatus?.invoke(id) }
                            )
                        }
                    }
                }

                BottomTab.AKUN -> AkunScreen(
                    tokenStore = tokenStore,
                    onUbahKataSandi = onUbahKataSandi,
                    onEditProfil = onEditProfil,
                    onLaporkanMasalah = onLaporkanMasalah,
                    onKeluarConfirmed = onKeluarConfirmed
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
    tenantName: String,
    unreadCount: Int,
    onNotifications: (() -> Unit)?,
    onPengajuanLayanan: (() -> Unit)?,
    onPengajuanSurat: (() -> Unit)?,
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
                .padding(horizontal = 20.dp, vertical = 20.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Image(
                painter = painterResource(R.drawable.icon_profile),
                contentDescription = "Profil",
                modifier = Modifier
                    .size(64.dp)
                    .clip(CircleShape)
                    .border(2.dp, Color.White.copy(alpha = 0.3f), CircleShape)
            )

            Spacer(Modifier.width(16.dp))

            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = "Halo, $userName",
                    fontFamily = PoppinsSemi,
                    fontSize = 20.sp,
                    color = Color.White,
                    maxLines = 1,
                    overflow = TextOverflow.Ellipsis
                )
                Text(
                    text = "Warga $tenantName",
                    fontFamily = PoppinsReg,
                    fontSize = 13.sp,
                    color = Color.White.copy(alpha = 0.8f)
                )
            }

            NotificationBell(
                unreadCount = unreadCount,
                onClick = onNotifications
            )
        }

        Spacer(Modifier.height(8.dp))

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
                    val imageUrl = com.example.homi.util.fixLocalhostUrl(item?.imageUrl)

                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(220.dp)
                            .clip(RoundedCornerShape(16.dp))
                            .clickable(
                                enabled = item != null && onDetailPengumumanClicked != null
                            ) {
                                item?.id?.let { id -> onDetailPengumumanClicked?.invoke(id) }
                            }
                    ) {
                        if (!imageUrl.isNullOrBlank()) {
                            val painter = rememberAsyncImagePainter(
                                model = imageUrl,
                                error = painterResource(R.drawable.img_pengumuman)
                            )
                            Image(
                                painter = painter,
                                contentDescription = item?.title ?: "Pengumuman",
                                contentScale = ContentScale.Crop,
                                modifier = Modifier.fillMaxSize()
                            )
                            
                            // Skeleton/Loading overlay simple
                            if (painter.state is AsyncImagePainter.State.Loading) {
                                Box(modifier = Modifier.fillMaxSize().background(Color.LightGray))
                            }
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

                        val dateText = DateUtils.formatIsoToId(item?.publishedAt ?: item?.createdAt)
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
                        icon = R.drawable.ic_doc,
                        title = "Pengajuan Surat",
                        onClick = onPengajuanSurat
                    )
                }
                item {
                    MenuButtonSymmetric(
                        icon = R.drawable.icon_pengajuan,
                        title = "Pengajuan Layanan",
                        onClick = onPengajuanLayanan
                    )
                }
                item {
                    // Meningkatkan ukuran icon karena aset aslinya memiliki padding internal yang besar
                    MenuButtonSymmetric(
                        icon = R.drawable.icon_pengaduan,
                        title = "Pengaduan Warga",
                        onClick = onPengaduan,
                        iconInnerSize = 54.dp
                    )
                }
                item {
                    // Meningkatkan ukuran icon agar seimbang dengan yang lain
                    MenuButtonSymmetric(
                        icon = R.drawable.icon_pembayaran,
                        title = "Pembayaran Iuran",
                        onClick = onPembayaran,
                        iconInnerSize = 54.dp
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
    iconInnerSize: androidx.compose.ui.unit.Dp = 36.dp
) {
    val shape = RoundedCornerShape(18.dp)
    val iconBoxSize = 58.dp

    Card(
        onClick = { onClick?.invoke() },
        enabled = onClick != null,
        shape = shape,
        colors = CardDefaults.cardColors(containerColor = BlueMain),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
        modifier = Modifier
            .fillMaxWidth()
            .height(84.dp)
    ) {
        Row(
            modifier = Modifier
                .fillMaxSize()
                .padding(horizontal = 16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier
                    .size(iconBoxSize)
                    .clip(RoundedCornerShape(14.dp))
                    .background(Color.White.copy(alpha = 0.12f)),
                contentAlignment = Alignment.Center
            ) {
                val tint = if (icon == R.drawable.ic_doc) androidx.compose.ui.graphics.ColorFilter.tint(Color.White) else null
                Image(
                    painter = painterResource(icon),
                    contentDescription = title,
                    modifier = Modifier.size(iconInnerSize),
                    contentScale = ContentScale.Fit,
                    colorFilter = tint
                )
            }

            Spacer(Modifier.width(18.dp))

            Text(
                text = title,
                fontFamily = PoppinsSemi,
                color = Color.White,
                fontSize = 17.sp,
                maxLines = 1
            )
        }
    }
}


/* ===== DIREKTORI SECTION (tetap seperti punyamu) ===== */
@Composable
private fun DirektoriSection(dirVm: DirectoryViewModel, tenantName: String) {
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
            text = "Berikut adalah nama dan alamat warga perumahan\n$tenantName",
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
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 4.dp),
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

                DirektoriCardList(items = state.items)
            }
        }
    }
}

@Composable
private fun DirektoriCardList(items: List<DirectoryItem>) {
    if (items.isEmpty()) {
        Box(
            modifier = Modifier.fillMaxWidth().padding(top = 40.dp),
            contentAlignment = Alignment.Center
        ) {
            Text(
                "Data direktori tidak ditemukan",
                fontFamily = PoppinsReg,
                color = HintGray,
                fontSize = 14.sp
            )
        }
    } else {
        LazyColumn(
            modifier = Modifier.fillMaxSize(),
            verticalArrangement = Arrangement.spacedBy(16.dp),
            contentPadding = PaddingValues(bottom = 24.dp)
        ) {
            items(items) { item ->
                Card(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 4.dp),
                    shape = RoundedCornerShape(20.dp),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    elevation = CardDefaults.cardElevation(defaultElevation = 3.dp),
                    border = BorderStroke(1.dp, Color(0xFFF1F5F9))
                ) {
                    Row(
                        modifier = Modifier
                            .padding(16.dp)
                            .fillMaxWidth(),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Surface(
                            shape = CircleShape,
                            color = BlueMain.copy(alpha = 0.1f),
                            modifier = Modifier.size(52.dp)
                        ) {
                            Box(contentAlignment = Alignment.Center) {
                                Text(
                                    text = (item.name.firstOrNull() ?: '?').toString().uppercase(),
                                    fontFamily = PoppinsSemi,
                                    color = BlueMain,
                                    fontSize = 20.sp
                                )
                            }
                        }

                        Spacer(Modifier.width(20.dp))

                        Column(modifier = Modifier.weight(1f)) {
                            Text(
                                text = item.name,
                                fontFamily = PoppinsSemi,
                                fontSize = 16.sp,
                                color = TextPrimary,
                                maxLines = 1,
                                overflow = TextOverflow.Ellipsis
                            )
                            Spacer(Modifier.height(4.dp))
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Icon(
                                    painter = painterResource(id = R.drawable.icon_home),
                                    contentDescription = null,
                                    tint = AccentOrange.copy(alpha = 0.7f),
                                    modifier = Modifier.size(14.dp)
                                )
                                Spacer(Modifier.width(6.dp))
                                Text(
                                    text = "Blok ${item.blok} • No. ${item.noRumah}",
                                    fontFamily = PoppinsReg,
                                    fontSize = 13.sp,
                                    color = HintGray
                                )
                            }
                        }
                        
                        Icon(
                            painter = painterResource(id = R.drawable.panahkembali),
                            contentDescription = null,
                            tint = Color.LightGray,
                            modifier = Modifier.size(16.dp).rotate(180f)
                        )
                    }
                }
            }
        }
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

    Surface(
        color = BlueMain, // Warna disamakan dengan tema utama
        shape = RoundedCornerShape(32.dp),
        shadowElevation = 8.dp,
        modifier = Modifier
            .padding(horizontal = 16.dp)
            .padding(bottom = 12.dp)
            .fillMaxWidth()
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(vertical = 10.dp, horizontal = 8.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            items.forEach { item ->
                val selected = item.tab == currentTab
                val iconId = if (selected) item.iconSelected else item.iconUnselected
                val color = if (selected) AccentOrange else Color.White

                Column(
                    modifier = Modifier
                        .weight(1f)
                        .clip(RoundedCornerShape(12.dp))
                        .clickable { onTabSelected(item.tab) },
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.Center
                ) {
                    Image(
                        painter = painterResource(id = iconId),
                        contentDescription = item.label,
                        modifier = Modifier.size(24.dp),
                        contentScale = ContentScale.Fit,
                        colorFilter = androidx.compose.ui.graphics.ColorFilter.tint(color)
                    )
                    
                    Spacer(Modifier.height(4.dp))
                    
                    Text(
                        item.label,
                        fontFamily = if (selected) PoppinsSemi else PoppinsReg,
                        fontSize = 11.sp,
                        color = color,
                        maxLines = 1,
                        textAlign = TextAlign.Center
                    )
                }
            }
        }
    }
}

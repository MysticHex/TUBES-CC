@php($online = $online ?? true)

<div class="server-card tilt card-3d h-100">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div class="text-uppercase small text-white-50 fw-semibold mb-1">Server Status</div>
            <div class="server-label">{{ $serverName }}</div>
        </div>
        <span class="badge rounded-pill {{ $online ? 'text-bg-success' : 'text-bg-danger' }} d-inline-flex align-items-center gap-2 px-3 py-2">
            <span class="status-dot {{ $online ? '' : 'down' }}"></span>
            {{ $online ? 'ONLINE' : 'OFFLINE' }}
        </span>
    </div>

    <div class="d-flex align-items-center gap-3 mt-4">
        <i class="bi bi-hdd-rack-fill fs-2"></i>
        <div class="small text-white-50">
            This request was handled by <strong class="text-white">{{ $serverName }}</strong>.
            Refresh behind the load balancer to see traffic shift between web servers.
        </div>
    </div>

    <i class="bi bi-cloud-fill server-node"></i>
</div>

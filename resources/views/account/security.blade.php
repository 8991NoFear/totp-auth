@extends('account.layouts.app')

@section('home-status', 'link-dark')
@section('profile-status', 'link-dark')
@section('security-status', 'active')

@section('main')
<div class="container-fluid">
    <div class="text-center mt-3">
        <h2>Security</h2>
        <p>Settings and recommendations to help you keep your account more securely</p>
    </div>

    <table class="table table-hover border rounded rounded-3 mt-5 align-middle">
        <thead>
        <tr>
            <th colspan="3" class="border-0 p-3">
            <h3 class="fs-5">Recent Security Activities</h3>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
            $activities = $user->securityActivities->all();
        ?>
        @foreach ($activities as $activity)
        <tr class="noselect" onclick="window.location='#';">
            <td class="col-sm-6">{{ $activity->action . ' - ' . $activity->device }}</td>
            <?php
                $dt = date("F j, Y, g:i a",strtotime($activity->created_at));
                $dt .= ' - ' . $activity->location;
            ?>
            <td>{{ $dt }}</td>
            <td class="text-end">
            <img src="{{ asset('/default-images/chevron_right_black_24dp.svg') }}" alt="arrow" srcset="">
            </td>
        </tr>
        @endforeach
        <tr class="noselect" onclick="window.location='#';">
            <td colspan="3" class="col-sm-6"><span class="text-primary">See all security activity ({{ count($activities) }})</span></td>
        </tr>
        </tbody>
    </table>

    <table class="table table-hover border rounded rounded-3 mt-5 align-middle">
        <thead>
        <tr>
            <th colspan="3" class="border-0 p-3">
            <h3 class="fs-5">Login to TOTP-AUTH</h3>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="noselect" onclick="window.location='#';">
            <td class="col-sm-6">Change password</td>
            <td>Last changed password at 26 Thg 7 - Hanoi, Vietnam</td>
            <td class="text-end"><img src="{{ asset('/default-images/chevron_right_black_24dp.svg') }}" alt="arrow" srcset=""></td>
        </tr>
        <tr class="noselect" onclick="window.location='{{ route('account.security.setup-google2fa') }}';">
            <td class="col-sm-6">Google two factor authentiaction (G2FA)</td>
            <td class="">
                @if ($user->secret_key != null)
                <span>Enabled </span>
                <img src="{{ asset('default-images/check_circle_black_24dp.svg') }}" class="align-top" alt="">
                @else
                <span>Not setup yet </span>
                <img src="{{ asset('/default-images/cancel_black_24dp.svg') }}" class="align-top" alt="">
                @endif
            </td>
            <td class="text-end"><img src="{{ asset('/default-images/chevron_right_black_24dp.svg') }}" alt="arrow" srcset=""></td>
        </tr>
        <tr class="noselect" onclick="window.location='{{ route('account.security.view-backup-code') }}';">
            <td class="col-sm-6">Backup codes</td>
            <td class="">
                <?php
                    $filtered = $user->backupCodes->filter(function ($value, $key) {
                        return $value->used_at == null;
                    });
                ?>
                @if ($user->backupCodes->isEmpty() )
                <span>Not setup yet </span>
                <img src="{{ asset('/default-images/cancel_black_24dp.svg') }}" class="align-top" alt="">
                @else
                <span>Available {{ $filtered->count() }} backup code(s) left</span>
                @endif
            </td>
            <td class="text-end"><img src="{{ asset('/default-images/chevron_right_black_24dp.svg') }}" alt="arrow" srcset=""></td>
        </tr>
        </tbody>
    </table>
</div>
@endsection
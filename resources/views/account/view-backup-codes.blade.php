@extends('account.layouts.2fa')

@section('title', 'Backup codes')

@section('body')

<div class="col-sm-6 offset-sm-3 mt-3">
    <div class="d-flex flex-row justify-content-between align-items-center border rounded-3 p-3 text-start">
        @if ($backupCodes->isNotEmpty())
        <p class="p-0 m-0 col-sm-8">Re-enable Google2FA to re-create backup codes</p>
        <a class="btn btn-primary" href="{{ route('account.security.setup-google2fa') }}">RE-CREATE</a>
        @else
        <p class="p-0 m-0 col-sm-8">Turn on Google2FA to create backup codes</p>
        <a class="btn btn-primary" href="{{ route('account.security.setup-google2fa') }}">TURN ON</a>
        @endif
    </div>
</div>
@if ($backupCodes->isNotEmpty())
<div class="col-sm-6 offset-sm-3">
    <table class="table table-hover border rounded rounded-3 p-3 mt-5 align-middle">
        <thead>
            <tr>
                <th colspan="3" class="border-0 p-3">
                    <h3 class="fs-5 text-center">List of Backup Codes</h3>
                </th>
            </tr>
        </thead>
        <tbody class="text-start">
            @forelse ($backupCodes as $backupCode)
            <tr class="noselect">
                <td class="col-sm-6">{{ $backupCode->code }}</td>
                @if ($backupCode->used_at != null)
                <td>Was used</td>
                @elseif ($backupCode->expired_at > date('Y-m-d H:i:s'))
                <td>Will be expired at {{ $backupCode->expired_at }}</td>
                @else
                <td>Was expired</td>
                @endif
            </tr>
            @empty
            <tr class="noselect">
                <td class="col-sm-6">There is no backup code</td>
            </tr>
            @endforelse

            <tr class="noselect">
                <td colspan="3" class="col-sm-6"><a class="text-primary" href="{{ route('account.security.download-backup-code') }}" style="text-decoration: none;">Download all
                        available backup code(s)</a></td>
            </tr>
        </tbody>
    </table>
</div>
@endif
<p class="mt-4 text-muted text-center">&copy; 2017–2021</p>

@section('body')

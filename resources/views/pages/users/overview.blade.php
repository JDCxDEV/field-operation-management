<x-base-layout>
  <user-overview
    user-find-url="{{ route("users.find", $user->id) }}"
    update-url="{{ route('settings.update', $user->id) }}"
    change-email-url={{ route('settings.changeEmail', $user->id) }}
    change-password-url={{ route('settings.changePassword', $user->id) }}
    update-two-auth-url={{ route('settings.updateTwoAuth', $user->id) }}    
    login-session-url="{{ route("users.login-sessions", $user->id) }}"
    user-id="{{ $user->id }}"
    activity-logs-fetch-url="{{ route('activity-logs.fetch') }}"
    theme-mode="{{ Auth::user()->theme_mode }}"
  ></user-overview>
</x-base-layout>
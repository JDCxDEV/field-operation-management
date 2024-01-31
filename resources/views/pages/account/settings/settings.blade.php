<x-base-layout>

    {{ theme()->getView('pages/account/_navbar', array('class' => 'mb-5 mb-xl-10', 'user' => isset($user) ? $user : auth()->user(), "isAuth" => $isAuth)) }}

    {{ theme()->getView('pages/account/settings/_profile-details', array('class' => 'mb-5 mb-xl-10', 'info' => isset($user) ? $user->info : auth()->user()->info, 'user' => isset($user) ? $user : auth()->user())) }}

    {{ theme()->getView('pages/account/settings/_signin-method', array('class' => 'mb-5 mb-xl-10', 'info' => isset($user) ? $user->info : auth()->user()->info, 'user' => isset($user) ? $user : auth()->user())) }}

</x-base-layout>

# ITRACKB4 - LA15

1. Your project answered one question last week and a second one this week. Name both questions, say which one your policy answers, and point at the file that answers the other.

Last week my movie app answered "Who are you?" through register and login, and this week it answers "What may you do?". My `MoviePolicy` answers the second one, and the first one is answered by `app/Http/Controllers/LoginController.php`, which uses `Auth::attempt` to check the email and password.

2. role is not in your User model's $fillable, and yet you set it and saved it in Tinker. Explain why both of those are true, and say what $fillable is actually guarding you against.

`$fillable` only filters mass assignment, which means an array handed to `create`, `fill` or `update`. In Tinker I set `$u->role = 'admin'` as a single property and called `save()`, so there was no array to filter. `$fillable` guards my `User` model against data arriving from a request, such as a hidden `role` field added to my register form.

3. A signed-in non-admin typing your create address gets a 403. A guest typing the same address gets your login page. Say which layer produced each response, and why the two are deliberately different.

The guest is stopped by the `auth` middleware on `movies.create`, which redirects to my `login` route before the controller runs. The signed-in non-admin passes the middleware, then `Gate::authorize('create', Movie::class)` in `MovieController` asks `MoviePolicy`, which returns false and produces the 403. They are different because a guest can still fix the problem by logging in, while a signed-in non-admin is already known and the answer is still no.

4. Your list and detail pages are still open to everybody, and your policy says so. Explain why that is a decision and not something you forgot.

My movie list is a catalogue, so visitors need to browse it and open a movie without an account. That is why `viewAny()` and `view()` in `MoviePolicy` return true, and why `index` and `show` in `MovieController` have no authorize call.

5. The same rule is read by your controller and by your list page. What would go wrong if you had written that role check in both places by hand instead of in a policy?

The role rule would exist in two places, and the copies could drift apart. If I changed the check in `MovieController` but forgot `movies/index.blade.php`, the page could show an Edit button that leads to a 403, or hide a button from someone who is allowed. With `MoviePolicy`, both the controller and `@can` ask the same method, so they always agree.

6. What does SESSION_LIFETIME count, what does it not count, and why was there no warning before you found yourself logged out?

`SESSION_LIFETIME` counts the minutes the session sits idle since my last request, and every request resets the clock. It does not count the time since I logged in. There was no warning because the server does not watch me, so it only finds out the session expired when I click a link and my next request arrives.
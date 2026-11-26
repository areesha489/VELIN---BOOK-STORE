@extends('layouts.store')

@section('title', 'Reader guide')
@section('description', 'How to register, search, order, review, and track a book on VELIN.')

@section('content')
    <section class="page-head wrap">
        <p class="kicker">Guide</p>
        <h1>How the reading room works</h1>
        <p class="lede">A short manual for the shop. The longer developer notes live with the project files.</p>
    </section>
    <div class="guide wrap">
        <article class="panel">
            <h2>Accounts</h2>
            <p>Create an account with your email. On this classroom machine you can also continue with a Google or GitHub demo profile from the sign-in page. Those buttons sign in a local social profile because live OAuth keys are not stored in the project.</p>
            <p>Sign out from your profile. If you forget the password, ask for a reset. Locally, the link is shown on the next screen and also written to the mail log. In production it would only be emailed.</p>
        </article>
        <article class="panel">
            <h2>Find a book</h2>
            <p>Search by title, author, genre, or ISBN. Filter by genre and author. Sort by popularity, price, or release date. Bestsellers and new arrivals are cuts of the same catalog, not separate shops.</p>
        </article>
        <article class="panel">
            <h2>Cart, list, checkout</h2>
            <p>Add copies, change the quantity, or remove a line. The total includes GST. Dispatch is ₹49, and free once the ticket crosses ₹999. Guests can fill a cart. Checkout asks you to sign in so the order has a name.</p>
            <p>Save a title to the wishlist if it is for later. At checkout, pick a saved address and payment method or add new ones. Card numbers are validated and discarded. The desk keeps the brand and last four digits, or a UPI id.</p>
        </article>
        <article class="panel">
            <h2>Orders and reviews</h2>
            <p>Every order has a number, a status, and a timeline from placed to delivered. A tracking code appears when the parcel ships. You can like another reader’s review. You can edit or delete your own.</p>
        </article>
        <article class="panel">
            <h2>Questions</h2>
            <details>
                <summary>Why was my quantity reduced?</summary>
                <p>The cart will not hold more copies than the shelf has, and never more than 20 of one title.</p>
            </details>
            <details>
                <summary>Can I pay with a real card?</summary>
                <p>No. This is a training shop. Use the test number 4242 4242 4242 4242. Nothing is sent to a bank.</p>
            </details>
            <details>
                <summary>Who updates delivery status?</summary>
                <p>An editor, from the desk. Readers can watch the timeline but cannot invent a status.</p>
            </details>
            <details>
                <summary>The page expired.</summary>
                <p>The form sat too long and the security token went stale. Go back, refresh, and submit again.</p>
            </details>
        </article>
    </div>
@endsection

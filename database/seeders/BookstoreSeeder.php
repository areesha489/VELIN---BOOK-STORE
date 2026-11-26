<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BookstoreSeeder extends Seeder
{
    public function run(): void
    {
        $admin = $this->person('Aisha Kapoor', 'admin@velin.store', 'VelinAdmin#2026', '+91 98200 00111', 'admin');
        $rohan = $this->person('Rohan Mehta', 'reader@velin.store', 'Reader#2026', '+91 98200 11023');
        $meera = $this->person('Meera Iyer', 'meera@velin.store', 'Meera#2026', '+91 98450 12211');

        $genres = collect([
            ['Fiction', 'Novels with a pulse and a point of view.', '#c4552a'],
            ['Mystery', 'Crimes, missing streets, and careful detectives.', '#1f4b4a'],
            ['Science', 'The living world, explained without the fog.', '#315c2b'],
            ['Romance', 'Love stories that earn the last page.', '#8c2f45'],
            ['History', 'Ports, kilns, and the long memory of trade.', '#8a5a2b'],
            ['Thriller', 'Signals, floods, and nights that do not sit still.', '#3d3428'],
            ['Essays', 'Short pieces for readers who like a sharp desk.', '#243056'],
            ['Science Fiction', 'Futures built out of paper, heat, and code.', '#204060'],
            ['Poetry', 'Compressed weather. Read it aloud.', '#5c3d6e'],
            ['Food', 'Kitchens, spice, and the heat that stays.', '#a15c2a'],
        ])->mapWithKeys(function (array $row) {
            [$name, $blurb, $accent] = $row;
            $genre = Genre::create([
                'name' => $name,
                'slug' => str($name)->slug()->toString(),
                'blurb' => $blurb,
                'accent' => $accent,
            ]);

            return [$genre->slug => $genre];
        });

        $authors = collect([
            ['Idris Calder', 'Lagos / Mumbai', 'Calder draws cities that appear only at low tide, then stays to meet the people who live there.'],
            ['Mei Laurent', 'Lyon', 'Laurent writes winter rooms, glass, and the fruit that refuses to travel.'],
            ['Arun Deshmukh', 'Pune', 'Deshmukh treats night markets like proofs: crowded, exact, and edible.'],
            ['Lila Okonkwo', 'Enugu', 'Okonkwo’s poems keep company with radio masts and rooftop birds.'],
            ['Jonah Voss', 'Hamburg', 'Voss writes thrillers in which the past sends a message and someone answers.'],
            ['Amara Sen', 'Kolkata', 'Sen’s essays start at the stove and end somewhere more honest.'],
            ['Sera Quinn', 'Dublin', 'Quinn builds science fiction out of archives, crews, and paper.'],
            ['Tomas Herrera', 'Mexico City', 'Herrera follows rivers that cities tried to pave over.'],
            ['Hana Ito', 'Kyoto', 'Ito studies rooms built for attention, including the ones with no clocks.'],
            ['Yusuf Rahman', 'Hyderabad', 'Rahman writes family histories that stay warm to the touch.'],
            ['Nia Brooks', 'Accra', 'Brooks writes romances for people who work with sound, stages, and timing.'],
            ['Elias Ward', 'Lisbon', 'Ward’s forgers are hired to draw borders, then asked to undo them.'],
            ['Priya Nair', 'Bengaluru', 'Nair looks at plants, heat, and the buildings that pretend to be neutral.'],
            ['Camille Duval', 'Montreal', 'Duval writes bookshops, unsent letters, and the drawers that keep them.'],
            ['Marcus Hale', 'Glasgow', 'Hale’s archivists catalog disasters and notice when the catalog changes.'],
            ['Fatima Noor', 'Kochi', 'Noor follows spice, ports, and the ocean roads that still smell like cargo.'],
            ['Owen Blake', 'Cape Town', 'Blake files dispatches from towns that were never on his map.'],
            ['Adele Frost', 'Oslo', 'Frost writes scientists who receive mail from the models they trained.'],
        ])->mapWithKeys(function (array $row) {
            [$name, $origin, $bio] = $row;
            $author = Author::create([
                'name' => $name,
                'slug' => str($name)->slug()->toString(),
                'origin' => $origin,
                'bio' => $bio,
            ]);

            return [$author->slug => $author];
        });

        $books = collect([
            ['The Salt Atlas', 'idris-calder', 'fiction', 'A city that exists at low tide.', 'A cartographer maps a city that only exists at low tide. Calder follows the people who live by that map, and the quiet debt it collects from anyone who trusts it. The streets redraw themselves each dawn. What stays is the work of remembering who asked to be found.', 'ember', 649, null, '978-81-90000-01-1', 336, '2024-02-11', 24, 420, true, true],
            ['Glass Orchard', 'mei-laurent', 'fiction', 'Fruit that cannot leave the greenhouse.', 'In a winter greenhouse outside Lyon, a glassblower grows fruit that cannot leave the building. Laurent writes the season the glass cracks, and the neighbors who pretend not to hear it. The orchard is a room with weather. The weather has opinions.', 'tide', 599, null, '978-81-90000-02-8', 288, '2023-11-02', 18, 380, true, false],
            ['Night Market Geometry', 'arun-deshmukh', 'science', 'Pattern, spice, and crowd.', 'Deshmukh walks the stalls of a night market with a mathematician’s eye, finding pattern in price, spice, and crowd. The book is a field guide you can read with one hand while the other holds a plate. Nothing here is abstract for long.', 'moss', 799, null, '978-81-90000-03-5', 264, '2026-08-18', 20, 140, false, false],
            ['Copper Birds', 'lila-okonkwo', 'poetry', 'Rooftops, masts, and mistaken trees.', 'Poems for rooftops, radio masts, and the birds that mistake them for trees. Okonkwo keeps the lines short enough to say on a stairwell. The collection is a weather report for people who live above the street.', 'sand', 449, null, '978-81-90000-04-2', 112, '2025-01-20', 3, 90, false, false],
            ['The Last Signal', 'jonah-voss', 'thriller', 'A message for a ship that already sank.', 'A lighthouse keeper receives a message meant for a ship that sank forty years ago. He answers it. Voss builds the dread out of routine: oil the lamp, log the hour, do not look too long at the water. By morning the log has a new hand.', 'night', 699, null, '978-81-90000-05-9', 352, '2024-06-01', 16, 510, true, false],
            ['Kitchen of Small Fires', 'amara-sen', 'food', 'A Kolkata kitchen, burner by burner.', 'Sen’s essays from a Kolkata kitchen where every burner is a decision about memory and heat. Recipes appear, then step aside for the people who taught them. This is a book about appetite, and about knowing when to turn the flame down.', 'citrus', 549, null, '978-81-90000-06-6', 240, '2026-09-02', 22, 160, false, false],
            ['Orbit of Paper', 'sera-quinn', 'science-fiction', 'When the servers forget, the press starts.', 'A space archive starts printing books when the servers forget. Quinn’s crew has to decide which stories to save on paper, and which ones the machine is allowed to lose. The ship smells like ink. The mission clock does not care.', 'ink', 749, null, '978-81-90000-07-3', 368, '2026-03-12', 14, 210, false, false],
            ['River Under the City', 'tomas-herrera', 'mystery', 'A detective follows a paved river.', 'A detective follows a river that was paved over, and the cases that still move with it. Herrera writes a city that sounds dry and is not. Every clue arrives damp. The ending does not tidy the map.', 'tide', 629, null, '978-81-90000-08-0', 320, '2024-09-09', 19, 300, true, false],
            ['A Manual for Quiet Rooms', 'hana-ito', 'essays', 'Libraries, trains, and a hotel with no clocks.', 'Ito’s field guide to rooms built for attention: libraries, train compartments, and one hotel with no clocks. The essays are practical, then suddenly personal. You can use this book to choose a seat. You can also use it to understand why you needed one.', 'wine', 499, null, '978-81-90000-09-7', 208, '2026-07-21', 26, 175, false, false],
            ['Red Kiln', 'yusuf-rahman', 'history', 'A pottery family and a kiln that stayed hot.', 'A history of a Hyderabad pottery family and the century their kiln refused to cool. Rahman stays with the hands, the clay, and the accounts book. The kiln is a character. It is also a workplace, which is the more interesting fact.', 'ember', 899, null, '978-81-90000-10-3', 412, '2023-04-18', 2, 260, false, false],
            ['Voltage & Velvet', 'nia-brooks', 'romance', 'Two engineers, one failing concert hall.', 'Two sound engineers fall in love while touring a failing concert hall. Brooks writes the work as carefully as the kiss: cables, cues, the last song before the lights misbehave. It is a romance for people who stay late to coil the leads.', 'wine', 479, 599, '978-81-90000-11-0', 304, '2026-10-01', 28, 190, false, false],
            ["The Cartographer's Debt", 'elias-ward', 'fiction', 'Draw a border. Then un-draw it.', 'Ward’s forger is hired to draw a border that will start a war, then asked to un-draw it. The novel moves by train, by ink, and by the kind of promise that looks like a map. Nobody leaves with the same coordinates.', 'moss', 729, null, '978-81-90000-12-7', 384, '2022-08-30', 11, 340, true, false],
            ['Moss Code', 'priya-nair', 'science', 'What the plants know about the heat.', 'Nair studies the way moss grows on server sheds in Bengaluru, and what the plants know about heat that the dashboards do not. The science is field science: knees, notebooks, and a roof at noon. The argument is calm and hard to shake.', 'moss', 679, null, '978-81-90000-13-4', 256, '2025-05-05', 17, 150, false, false],
            ['Letters Never Sent', 'camille-duval', 'romance', 'A drawer of letters dated before she was born.', 'A Montreal bookseller finds a drawer of letters addressed to her, dated before she was born. Duval lets the shop stay open while the mystery warms up. This is a love story with inventory, dust, and very good stationary.', 'sand', 429, 549, '978-81-90000-14-1', 276, '2024-12-14', 21, 280, false, false],
            ['Blackwater Index', 'marcus-hale', 'thriller', 'The catalog of a flood starts editing itself.', 'An archivist in Glasgow catalogs a flood, then realizes the index is changing overnight. Hale writes procedure as suspense. Each revised entry removes a name or returns one. The building’s lower floor is still wet.', 'night', 759, null, '978-81-90000-15-8', 340, '2026-06-11', 13, 230, false, false],
            ['Spice Routes', 'fatima-noor', 'history', 'Pepper, clove, and the ports that remember.', 'Noor retells the ocean roads of pepper and clove through the ports that still smell of them. The history is specific: weights, winds, and the people who were left off the plaque. You finish hungry, and better informed than that.', 'citrus', 849, null, '978-81-90000-16-5', 390, '2021-03-03', 15, 410, true, false],
            ['Field Notes from Nowhere', 'owen-blake', 'essays', 'Towns that do not appear on the issued map.', 'Blake’s dispatches from towns that do not appear on the maps he was issued. The essays are travel writing with the tourism scraped off. He is a good guest. He does not pretend the visit fixed anything.', 'ink', 539, null, '978-81-90000-17-2', 232, '2025-09-19', 18, 120, false, false],
            ['The Warm Algorithm', 'adele-frost', 'science-fiction', 'A climate model starts writing letters.', 'A climate model starts writing letters to the scientist who trained it. Frost keeps the science legible and the letters unnervingly polite. The question is not whether the model is alive. The question is who has to answer.', 'ember', 699, null, '978-81-90000-18-9', 312, '2026-04-04', 8, 200, false, false],
        ])->mapWithKeys(function (array $row) use ($authors, $genres) {
            [
                $title, $authorSlug, $genreSlug, $excerpt, $description, $cover, $price, $compare, $isbn, $pages, $published, $stock, $popularity, $bestseller, $featured,
            ] = $row;

            $book = Book::create([
                'author_id' => $authors[$authorSlug]->id,
                'genre_id' => $genres[$genreSlug]->id,
                'title' => $title,
                'slug' => str($title)->slug()->toString(),
                'excerpt' => $excerpt,
                'description' => $description,
                'cover_key' => $cover,
                'price' => $price,
                'compare_at' => $compare,
                'isbn' => $isbn,
                'pages' => $pages,
                'language' => 'English',
                'format' => $price >= 800 ? 'Hardback' : 'Paperback',
                'published_at' => $published,
                'stock' => $stock,
                'popularity' => $popularity,
                'is_bestseller' => $bestseller,
                'is_featured' => $featured,
            ]);

            return [$book->slug => $book];
        });

        $rohanAddress = $this->address($rohan, 'Home', 'Rohan Mehta', '+91 98200 11023', '14 Rope Walk Lane', 'Kala Ghoda', 'Mumbai', 'Maharashtra', '400001');
        $meeraAddress = $this->address($meera, 'Studio', 'Meera Iyer', '+91 98450 12211', '22 Indiranagar 100 Feet Road', null, 'Bengaluru', 'Karnataka', '560038');

        $rohanCard = PaymentMethod::create([
            'user_id' => $rohan->id,
            'type' => 'card',
            'brand' => 'Visa',
            'last_four' => '4242',
            'holder_name' => 'ROHAN MEHTA',
            'exp_month' => 8,
            'exp_year' => 2028,
            'is_default' => true,
        ]);

        PaymentMethod::create([
            'user_id' => $meera->id,
            'type' => 'upi',
            'upi_id' => 'meera.reads@okaxis',
            'holder_name' => 'Meera Iyer',
            'is_default' => true,
        ]);

        $this->history(
            $rohan,
            'VL260112RK01',
            'delivered',
            'VELINDL12345',
            'Delhivery',
            $rohanAddress,
            $rohanCard,
            [
                [$books['the-salt-atlas'], 1],
                [$books['glass-orchard'], 1],
            ],
            '2026-01-12 10:04:00',
            [
                ['placed', 'Order received at the VELIN desk.', '2026-01-12 10:04:00'],
                ['confirmed', 'The desk confirmed the copies.', '2026-01-12 14:10:00'],
                ['packed', 'Packed in the bindery room.', '2026-01-13 11:05:00'],
                ['shipped', 'Handed to Delhivery.', '2026-01-13 18:40:00'],
                ['out_for_delivery', 'Out for delivery in south Mumbai.', '2026-01-15 09:15:00'],
                ['delivered', 'Delivered to Rope Walk Lane.', '2026-01-15 16:20:00'],
            ],
        );

        $this->history(
            $rohan,
            'VL261120RK02',
            'shipped',
            'VELINDL99881',
            'India Post',
            $rohanAddress,
            $rohanCard,
            [
                [$books['blackwater-index'], 1],
            ],
            '2026-11-18 19:12:00',
            [
                ['placed', 'Order received at the VELIN desk.', '2026-11-18 19:12:00'],
                ['confirmed', 'The desk confirmed the copy.', '2026-11-18 20:02:00'],
                ['packed', 'Packed in the bindery room.', '2026-11-19 12:30:00'],
                ['shipped', 'Handed to India Post.', '2026-11-20 09:45:00'],
            ],
        );

        $meeraPay = PaymentMethod::query()->where('user_id', $meera->id)->first();
        $this->history(
            $meera,
            'VL260815MI01',
            'delivered',
            'VELINBL44110',
            'Delhivery',
            $meeraAddress,
            $meeraPay,
            [
                [$books['spice-routes'], 1],
            ],
            '2026-08-02 08:40:00',
            [
                ['placed', 'Order received at the VELIN desk.', '2026-08-02 08:40:00'],
                ['confirmed', 'The desk confirmed the copy.', '2026-08-02 13:00:00'],
                ['packed', 'Packed in the bindery room.', '2026-08-03 10:20:00'],
                ['shipped', 'Handed to Delhivery.', '2026-08-03 17:10:00'],
                ['out_for_delivery', 'Out for delivery in Indiranagar.', '2026-08-06 09:05:00'],
                ['delivered', 'Delivered to the studio.', '2026-08-06 14:48:00'],
            ],
        );

        $salt = Review::create([
            'user_id' => $meera->id,
            'book_id' => $books['the-salt-atlas']->id,
            'rating' => 5,
            'title' => 'I kept losing the street and finding it again',
            'body' => 'I read this on the train and missed my stop, which feels like the right way to meet a city that moves. Calder never explains the tide too early. The last chapter is generous without being soft.',
        ]);

        Review::create([
            'user_id' => $meera->id,
            'book_id' => $books['the-last-signal']->id,
            'rating' => 4,
            'title' => 'The logbook is the scary part',
            'body' => 'Voss trusts the routine. That is why the changed handwriting lands. I wanted one more chapter with the keeper after the answer, but the book knows when to switch the lamp off.',
        ]);

        Review::create([
            'user_id' => $meera->id,
            'book_id' => $books['spice-routes']->id,
            'rating' => 5,
            'title' => 'Specific enough to smell',
            'body' => 'Noor refuses the postcard version of the ocean trade. Weights, winds, and the names left off the plaque. I cooked from the margins the same night, which is not a metaphor.',
        ]);

        Review::create([
            'user_id' => $rohan->id,
            'book_id' => $books['river-under-the-city']->id,
            'rating' => 4,
            'title' => 'Damp in the best way',
            'body' => 'Herrera writes a paved city that is still a river. The case is clean enough to follow and strange enough to keep. I closed it and checked the floor, which is a compliment.',
        ]);

        $letters = Review::create([
            'user_id' => $rohan->id,
            'book_id' => $books['letters-never-sent']->id,
            'rating' => 5,
            'title' => 'A shop novel that respects the shop',
            'body' => 'Duval lets the bookseller do her job while the drawer warms up. The romance is not a pause in the work. It is what happens between customers, which is when real life actually fits.',
        ]);

        Review::create([
            'user_id' => $rohan->id,
            'book_id' => $books['glass-orchard']->id,
            'rating' => 3,
            'title' => 'Beautiful, and a little airless',
            'body' => 'The greenhouse is unforgettable and the sentences are exact. I wanted one character to leave the glass sooner. Still glad I carried it home. The fruit scene is the one I keep quoting.',
        ]);

        ReviewLike::create(['user_id' => $rohan->id, 'review_id' => $salt->id]);
        ReviewLike::create(['user_id' => $meera->id, 'review_id' => $letters->id]);

        $rohan->wishlists()->create(['book_id' => $books['orbit-of-paper']->id]);
        $meera->wishlists()->create(['book_id' => $books['a-manual-for-quiet-rooms']->id]);

        $admin->forceFill(['phone' => '+91 98200 00111'])->save();
    }

    private function person(string $name, string $email, string $password, string $phone, string $role = 'customer'): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
            'role' => $role,
            'provider' => 'email',
            'is_active' => true,
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        return $user;
    }

    private function address(User $user, string $label, string $recipient, string $phone, string $line1, ?string $line2, string $city, string $state, string $postal): Address
    {
        return Address::create([
            'user_id' => $user->id,
            'label' => $label,
            'recipient' => $recipient,
            'phone' => $phone,
            'line1' => $line1,
            'line2' => $line2,
            'city' => $city,
            'state' => $state,
            'postal_code' => $postal,
            'country' => 'India',
            'is_default' => true,
        ]);
    }

    /**
     * @param  array<int, array{0: Book, 1: int}>  $lines
     * @param  array<int, array{0: string, 1: string, 2: string}>  $events
     */
    private function history(User $user, string $number, string $status, string $tracking, string $carrier, Address $address, PaymentMethod $payment, array $lines, string $createdAt, array $events): void
    {
        $subtotal = 0.0;
        foreach ($lines as [$book, $quantity]) {
            $subtotal += (float) $book->price * $quantity;
        }
        $shipping = $subtotal >= 999 ? 0 : 49;

        $order = Order::create([
            'user_id' => $user->id,
            'number' => $number,
            'status' => $status,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
            'tracking_code' => $tracking,
            'carrier' => $carrier,
            'address_snapshot' => [
                'recipient' => $address->recipient,
                'phone' => $address->phone,
                'line1' => $address->line1,
                'line2' => $address->line2,
                'city' => $address->city,
                'state' => $address->state,
                'postal_code' => $address->postal_code,
                'country' => $address->country,
            ],
            'payment_snapshot' => [
                'type' => $payment->type,
                'brand' => $payment->brand,
                'last_four' => $payment->last_four,
                'upi_id' => $payment->upi_id,
                'holder_name' => $payment->holder_name,
            ],
        ]);

        $order->forceFill([
            'created_at' => Carbon::parse($createdAt),
            'updated_at' => Carbon::parse($events[array_key_last($events)][2]),
        ])->save();

        foreach ($lines as [$book, $quantity]) {
            OrderItem::create([
                'order_id' => $order->id,
                'book_id' => $book->id,
                'title' => $book->title,
                'author_name' => $book->author->name,
                'price' => $book->price,
                'quantity' => $quantity,
            ]);
        }

        foreach ($events as [$eventStatus, $message, $at]) {
            OrderEvent::create([
                'order_id' => $order->id,
                'status' => $eventStatus,
                'message' => $message,
                'happened_at' => Carbon::parse($at),
            ]);
        }
    }
}

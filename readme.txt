=== Block Member Posting – Read-Only Members for BuddyPress ===
Contributors: giannis4, thewpgarden
Tags: buddypress, buddyboss, moderation, members, community
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.1.3
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=J7GGEGDD4XV5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Stop chosen members, or a whole member type, from posting activity updates and comments on BuddyPress and BuddyBoss. They can still read.

== Description ==

Sometimes a member should stay in the community but stop posting: a new account still on probation, a member who keeps breaking the rules, or a whole group of users who should read and not write.

Block Member Posting makes those members read-only on the activity feed. Block one member from their profile screen, or a whole member type at once, and decide separately whether they can still post updates and whether they can still comment. They keep their account and can still browse, so you moderate without banning anyone.

**Requires:** [BuddyPress](https://wordpress.org/plugins/buddypress/) with the Activity component, or BuddyBoss Platform.

**Works with:** BuddyPress member types and BuddyBoss profile types, on the BuddyPress Nouveau template pack (the default) and BuddyBoss.

= Who it is for =

🎓 **Schools and online courses**: students read announcements while only teachers post.

🏢 **Membership sites and intranets**: staff publish updates, everyone else follows them.

🛡️ **Moderators of busy communities**: put a member who keeps breaking the rules on read-only instead of deleting the account.

= How it works =

1. Go to **Users**, edit a member, and under "Block Member From Posting" tick the option to block new posts, the option to block comments, or both.
2. To block a whole group, edit a member type (BuddyPress) or a profile type (BuddyBoss) and tick the same options there.
3. Blocked members no longer see the post form or the comment and reply buttons on the activity feed.

= Features =

🚫 **Block new posts**: the "What's new" activity form is removed for blocked members.

💬 **Block comments and replies**: the comment and reply buttons are removed from every activity item.

👤 **Block single members** from their profile screen in the admin.

👥 **Block a whole member type** in BuddyPress, or a profile type in BuddyBoss, in one step. New members of that type are blocked automatically.

🔀 **Posting and commenting are separate**, so a member can be allowed to comment but not post, or the other way round.

📋 **See who is blocked**: the Users screen gets Posting and Commenting columns, plus "Blocked Posting" and "Blocked Commenting" filters.

💻 **Developer filters** such as `bp_is_member_posting_blocked` and `bp_is_member_commenting_blocked` let you change who counts as blocked.

= Documentation and support =

📖 [Documentation](https://thewpgarden.com/docs/bp-block-member-posting/): setup and a guide for every feature, from [blocking a member](https://thewpgarden.com/docs/bp-block-member-posting/block-a-member-from-posting/) to [blocking a member type](https://thewpgarden.com/docs/bp-block-member-posting/block-a-member-type-from-posting/) and [finding blocked members](https://thewpgarden.com/docs/bp-block-member-posting/find-blocked-members/).

🛟 [Support forum](https://wordpress.org/support/plugin/bp-block-member-posting/): questions and bug reports. We answer every topic.

✉️ [Contact us](https://thewpgarden.com/contact-us/): anything that is not a support question.

= Like it? =

A [5-star review](https://wordpress.org/support/plugin/bp-block-member-posting/reviews/#new-post) is the most useful thing you can do for a small plugin. It takes a minute and helps other community owners find it.

= More from The WP Garden =

Block Member Posting is made and maintained by [The WP Garden](https://thewpgarden.com/), small focused plugins for WooCommerce, BuddyPress and WordPress.

* [Pinned Feed Notices](https://wordpress.org/plugins/bp-pinned-feed-notices/): pinned announcements on the BuddyPress activity feed.
* [Terms and Conditions per Product](https://wordpress.org/plugins/terms-and-conditions-per-product/): a required Terms checkbox per product.
* [Checkout Guard](https://wordpress.org/plugins/checkout-guard/): block fake and spam orders at checkout.
* [SubPortal](https://wordpress.org/plugins/subportal/): a self-service portal for WooCommerce Subscriptions.
* [Peter's Post Notes](https://wordpress.org/plugins/peters-post-notes/): admin notes on posts and pages.

== Installation ==

1. In your WordPress admin, go to **Plugins > Add New Plugin**, search for "Block Member Posting", then click **Install Now** and **Activate**.
2. BuddyPress with the Activity component, or BuddyBoss Platform, must be active.
3. Go to **Users**, edit a member, and use the "Block Member From Posting" options. For a whole group, edit a member type or profile type instead.

== Frequently Asked Questions ==

= How do I stop a member from posting on BuddyPress? =

Go to **Users** in the admin, edit the member, and under "Block Member From Posting" tick the option to block them from making new posts. The activity post form disappears for them straight away.

= Can I block a whole member type or profile type? =

Yes. On BuddyPress, edit the member type under **Users > Member Types**. On BuddyBoss, edit the profile type. Tick the posting and commenting options there and every member of that type is blocked, including members who join it later.

= Can a member comment but not post, or post but not comment? =

Yes. Posting and commenting are two separate options, both for single members and for member types.

= Can blocked members still read the activity feed? =

Yes. They keep their account and can browse the site and the feed as before. Only the post form and the comment and reply buttons are removed.

= Is the member told they are blocked? =

No. The form and buttons are simply not shown to them.

= How do I see which members are blocked? =

The **Users** screen has Posting and Commenting columns, and "Blocked Posting" and "Blocked Commenting" links at the top to list only those members.

= Does it work with BuddyBoss? =

Yes. It works with BuddyBoss Platform as well as BuddyPress, and supports BuddyBoss profile types.

= Does it block posting through the REST API or a mobile app? =

No. It removes the post form and the comment and reply buttons on the website, which is how members post there. Posts made through the REST API or a separate app are not blocked.

= Where is the documentation? =

At [thewpgarden.com/docs/bp-block-member-posting](https://thewpgarden.com/docs/bp-block-member-posting/): setup, plus a guide for every feature.

= How do I get support? =

Open a topic in the [WordPress.org support forum](https://wordpress.org/support/plugin/bp-block-member-posting/). For anything else, [contact us](https://thewpgarden.com/contact-us/).

= Can I translate Block Member Posting? =

Yes. You can translate [Block Member Posting](https://translate.wordpress.org/projects/wp-plugins/bp-block-member-posting/) into your language on translate.wordpress.org.

== Screenshots ==
1. The activity post form is removed for blocked members.
2. The comment and reply buttons are removed for blocked members.
3. The Users screen, with Posting and Commenting columns and "Blocked Posting" and "Blocked Commenting" filters.
4. Blocking a single member from posting and commenting on their profile screen.
5. Blocking a whole profile type from posting (BuddyBoss).
6. Blocking a whole member type from posting (BuddyPress).

== Changelog ==

= 1.1.3 =
* Security: Use prepared statements for the blocked-members admin filter query.
* Fix: Escape dynamic member/profile names with esc_html() instead of the translation functions.
* Fix: Remove duplicate member-type save handler that stored term meta twice.
* Fix: Remove enqueue of an unregistered admin script handle.
* Fix: Print the correct notice when BuddyPress is disabled on the admin list page.
* Fix: Resolve PHP 8 "undefined array key" warnings and replace the deprecated FILTER_SANITIZE_STRING.

= 1.1.2 =
* Update: WordPress 6.7.1
* Update: BuddyPress 14.3.3

= 1.1.0 =
* Add the post and comment blocking of specific member types (BuddyPress) or profile types (BuddyBoss).
* Block comment replies for members that have their comments blocked.

= 1.0.1 =
* Fix issue with function prefix

= 1.0 =
* First Edition release

== Upgrade Notice ==

= 1.1.3 =
Security and PHP 8 fixes: the blocked-members filter now uses prepared statements, member names are escaped correctly, and the PHP 8 warnings are gone.

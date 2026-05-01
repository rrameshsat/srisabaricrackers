# Changelog

## Patch 3.2 - Admin UI cleanup
- Admin billing UI: simplified initialization and binding for billing state. The billing state selection now resets when the billing country changes, and the bill_state_id remains the source of truth for billing state.
- UI groundwork laid for Phase 3: India-focused billing with bill_state_id, dynamic country/state loading via /country-states/{country_id}.
- Regression notes added (see docs/patches/patch-3-2-3-3-regression-notes.md).

## Patch 3.3 - Tests aligned with Phase 3
- Updated Registration test suite to rely on bill_state_id for India and added USA/Europe test scaffolding behind environment gates.
- Added Admin backend test for updating India billing/shipping addresses via the admin path using bill_state_id.
- Added backend/admin Dusk test scaffold to exercise UI behavior for non-India locale (optional gating).
- Added regression notes entry (docs/patches/patch-3-2-3-3-regression-notes.md) for quick human reference.

Note: The Europe/USA tests are gated by environment variables RUN_EURO_TESTS and RUN_USA_TESTS to avoid flakiness in India-only environments. Enable these in CI when cross-locale coverage is desired.

# Patch 3.2-3.3 Regression Notes

- Scope: Harden admin UI binding for billing state to bill_state_id and validate registration/admin flows across India and USA (planned for future activation).
- India (current production scope):
  - Billing state must be tied to bill_state_id (not state_id) for all billing-related fields.
  - Admin UI: On country change, bill_state_id must be cleared or reset when a new country is selected; initial load should preload based on existing bill_state_id if present.
  - Registration: If bill_country is India, bill_state_id must be present to pass validation (edge cases tested in IndiaStatesEdgeCaseTest).
  - Dependent country-state loading endpoint /country-states/{country_id} continues to be used to populate UI select options.
- United States (future testing scope):
  - When bill_country is United States, bill_state_id should be used to bind the selected state (California in tests).
  - Admin and Registration flows should load US states via /country-states/{country_id} and persist via bill_state_id.
  - Tests are gated behind RUN_EURO_TESTS flag to avoid failing in India-only environments; enable in CI when coverage is desired.
- Testing approach:
  - Unit tests verify the relationship between User and billingState for India and USA.
  - Feature tests verify registration flow uses bill_state_id when India has a state and for USA. Admin tests cover admin updates to bill_state_id for India and USA.
  - Dusk tests exercise browser-level validation for country-state loading and binding in admin UI.

- CI/Local execution:
  - Ensure composer dependencies are installed before running tests.
  - US/EU tests are guarded via RUN_EURO_TESTS env var to avoid failing India-only environments.

- Notes for future: When India-only operation is released, we can strip the Euro/USA tests and gating to simplify test matrix.

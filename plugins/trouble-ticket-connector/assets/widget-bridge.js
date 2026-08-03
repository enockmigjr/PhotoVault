(function () {
  "use strict";
  var config = window.TroubleTicketConnector;
  if (!config || !config.authenticated || !config.assertionUrl || !config.nonce) return;

  fetch(config.assertionUrl, {
    method: "POST",
    credentials: "same-origin",
    cache: "no-store",
    headers: { "X-WP-Nonce": config.nonce, "Content-Type": "application/json" },
    body: "{}"
  }).then(function (response) {
    return response.ok ? response.json() : null;
  }).then(function (payload) {
    var assertion = payload && payload.success && payload.data && payload.data.assertion;
    if (typeof assertion !== "string") return;
    identifyWhenReady(assertion, 0);
  }).catch(function () {
    // The widget remains usable through its normal public verification flow.
  });

  function identifyWhenReady(assertion, attempt) {
    if (window.TelecomSupportWidget && typeof window.TelecomSupportWidget.identify === "function") {
      window.TelecomSupportWidget.identify(assertion);
      return;
    }
    if (attempt < 40) window.setTimeout(function () { identifyWhenReady(assertion, attempt + 1); }, 100);
  }
})();

describe("Authentication Redirect Logic", () => {
    it("redirects based on token existence", () => {
        cy.visit("http://127.0.0.1:8000");

        // Use cy.getCookie and then handle the result
        cy.getCookie("ACCESS_TOKEN").then((cookie) => {
            if (!cookie) {
                // CASE: Token DOES NOT exist
                cy.url().should("include", "/");
                cy.get("[data-testid='login-title']").should("exist");
            } else {
                // CASE: Token DOES exist
                cy.url().should("include", "/auth");
                cy.get("nav").should("be.visible");
            }
        });
    });
});

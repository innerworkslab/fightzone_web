describe("Authentication Flow", () => {
    it("should login and then logout successfully", () => {
        // --- LOGIN SECTION ---
        cy.visit("http://127.0.0.1:8000");
        cy.get('input[id="username"]').type("superadmin");
        cy.get('input[id="password"]').type("password");
        cy.get('button[type="submit"]').click();

        cy.url().should("include", "/auth");
        cy.getCookie("ACCESS_TOKEN").should("exist");

        // --- LOGOUT SECTION ---
        // Now that we are logged in, the button should be there
        cy.get('button[data-testid="logout-btn"]').should("be.visible").click();
        cy.get("[data-testid='confirm-modal-content']").should("be.visible");
        cy.get('button[data-testid="confirm-btn"]').click();

        cy.getCookie("ACCESS_TOKEN").should("not.exist");
        cy.url().should("eq", "http://127.0.0.1:8000/");
    });
});

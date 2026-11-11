const shipping = value => cy.get(`[name="shipping_method"][value="${value}"]`).check();
const payment = value => cy.get(`[name="payment_method"][value="${value}"]`).check();
const submit = () => cy.get('.checkout-submit').click();
const card = number => {
    payment('demo_card');
    cy.get('#card_number').type(number);
    cy.get('#card_expiry').type(`12/${String(new Date().getFullYear() + 1).slice(-2)}`);
    cy.get('#card_cvc').type('123');
};

describe('Rendelés és demófizetés', () => {
    beforeEach(() => {
        cy.task('resetDatabase');
        cy.visit('/login');
        cy.get('#email').type('cypress@example.test');
        cy.get('#password').type('Cypress-Test-123!', { log: false });
        cy.get('form').submit();
        cy.location('pathname').should('eq', '/dashboard');
        cy.visit('/checkout');
        cy.get('#phone').type('0612345678');
    });

    [320, 375, 1440].forEach(width => {
        it(`a segítség lenyitható és a fizetési mezők elférnek ${width}px szélességen`, () => {
            cy.viewport(width, 900);
            payment('demo_card');
            cy.get('[data-cy="payment-help"]').should('not.have.attr', 'open');
            cy.get('.demo-card-examples').should('not.be.visible');
            cy.get('[data-cy="payment-help"] summary').click();
            cy.get('.demo-card-examples').should('be.visible').and('contain', '4242');
            cy.document().then(document => expect(document.documentElement.scrollWidth).to.be.at.most(width + 1));
            cy.get('[data-cy="payment-help"] summary').click();
            cy.get('.demo-card-examples').should('not.be.visible');
            cy.get('#card_number').type('4242424242424242');
            payment('cod');
            cy.get('#demo-card-fields').should('not.be.visible');
            cy.get('#card_number').should('have.value', '').and('be.disabled');
        });
    });

    it('személyes átvételnél cím nélkül rendel, váltáskor helyesen számolja a szállítást', () => {
        cy.get('#address').should('be.visible').and('have.attr', 'required');
        shipping('pickup');
        cy.get('#address').should('not.be.visible').and('be.disabled').and('not.have.attr', 'required');
        cy.get('#shipping-fee').should('have.text', '0');
        shipping('courier');
        cy.get('#address').should('be.visible').and('not.be.disabled').and('have.attr', 'required');
        shipping('pickup');
        submit();
        cy.contains('Köszönjük a rendelésed!').should('be.visible');
        cy.task('databaseState').then(state => {
            expect(state.orders).to.have.length(1);
            expect(state.orders[0].address).to.equal(null);
            expect(Number(state.orders[0].shipping_fee)).to.equal(0);
            expect(state.stock).to.equal(4);
        });
    });

    it('házhoz szállításnál megköveteli, majd elmenti a címet', () => {
        submit();
        cy.get('#address').then(input => expect(input[0].validity.valueMissing).to.equal(true));
        cy.task('databaseState').its('orders').should('have.length', 0);
        cy.get('#address').type('3300 Eger, Teszt utca 1.');
        submit();
        cy.contains('Köszönjük a rendelésed!').should('be.visible');
        cy.task('databaseState').then(state => {
            expect(state.orders[0].address).to.equal('3300 Eger, Teszt utca 1.');
            expect(state.orders[0].shipping_method).to.equal('courier');
        });
    });

    [['4242424242424242', 'Visa'], ['5555555555554444', 'Mastercard']].forEach(([number, brand]) => {
        it(`${brand} sikeres tesztfizetés, oldalfrissítés nem duplázza a rendelést`, () => {
            shipping('pickup');
            card(number);
            submit();
            cy.contains('Sikeres szimuláció').should('be.visible');
            cy.contains('Tesztkártya:').should('contain', brand).and('contain', number.slice(-4));
            cy.reload();
            cy.contains('Sikeres szimuláció').should('be.visible');
            cy.task('databaseState').then(state => {
                expect(state.orders).to.have.length(1);
                expect(state.orders[0].payment_status).to.equal('demo_approved');
                expect(state.orders[0]).not.to.have.property('card_number');
                expect(state.orders[0]).not.to.have.property('card_cvc');
                expect(state.stock).to.equal(4);
                expect(state.cartQuantity).to.equal(0);
            });
        });
    });

    ['4000000000000002', '5105105105105100', '4242424242424241'].forEach(number => {
        it(`elutasított vagy hibás kártya nem módosítja a készletet (${number.slice(-4)})`, () => {
            shipping('pickup');
            card(number);
            submit();
            cy.location('pathname').should('eq', '/checkout');
            cy.contains(number.endsWith('4241') ? 'ellenőrző összege hibás' : 'demófizetés elutasítva').should('be.visible');
            cy.get('#card_number').should('have.value', '');
            cy.get('#card_cvc').should('have.value', '');
            cy.task('databaseState').then(state => {
                expect(state.orders).to.have.length(0);
                expect(state.stock).to.equal(5);
                expect(state.cartQuantity).to.equal(1);
            });
        });
    });

    it('a kosár mennyisége növelhető, a termék eltávolítható', () => {
        cy.visit('/cart');
        cy.intercept('POST', '/cart/increase/*').as('increase');
        cy.get('[data-action="increase"]').click();
        cy.wait('@increase').its('response.statusCode').should('eq', 200);
        cy.get('.cart-quantity span').should('have.text', '2');
        cy.intercept('POST', '/cart/remove/*').as('remove');
        cy.get('[data-action="remove"]').click();
        cy.wait('@remove');
        cy.get('.empty-cart').should('be.visible');
        cy.task('databaseState').its('cartQuantity').should('eq', 0);
    });
});

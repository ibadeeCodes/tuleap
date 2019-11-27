/*
 * Copyright (c) Enalean, 2019 - present. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see http://www.gnu.org/licenses/.
 *
 */
import * as getters from "./getters";
import { State } from "./type";

describe("getters", () => {
    describe("is_template_selected", () => {
        it(`Should return false when the selected template is null `, () => {
            const state: State = { selected_template: null, tuleap_templates: [] };
            expect(getters.is_template_selected(state)).toBe(false);
        });
        it(`Should return true when a template is choosen `, () => {
            const state: State = {
                selected_template: {
                    title: "scrum",
                    description: "scrum desc",
                    name: "scrum_template",
                    svg: "<svg></svg>"
                },
                tuleap_templates: []
            };
            expect(getters.is_template_selected(state)).toBe(true);
        });
    });
});
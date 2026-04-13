---
name: Stitch Website Design
description: Skill for designing and generating website screens using Google Stitch MCP tools.
---

# Stitch Website Design Skill

This skill provides instructions on how to use Google Stitch to design and iterate on website UIs dynamically.

## Core Capabilities
- **Create Projects**: Centralize your designs by creating a Stitch Project (`mcp_StitchMCP_create_project`).
- **Generate Screens**: Turn text prompts into beautiful UI designs (`mcp_StitchMCP_generate_screen_from_text`).
- **Edit Screens**: Modify existing designs by specifying screen IDs and providing editing prompts (`mcp_StitchMCP_edit_screens`).
- **Generate Variants**: Explore different creative directions by generating variants of an existing screen (`mcp_StitchMCP_generate_variants`).

## Workflow for Designing a Website
1. **Initialize Project**: 
   - Check existing projects using `mcp_StitchMCP_list_projects`.
   - Create a new project using `mcp_StitchMCP_create_project` (e.g., `title: "My Web App"`). Get the `projectId`.

2. **Generate Initial Screens**:
   - Use `mcp_StitchMCP_generate_screen_from_text` with the `projectId` and a detailed `prompt`.
   - Specify `deviceType` (MOBILE, DESKTOP, TABLET) depending on the target platform.
   - You will receive a `screenId` once completed. Note that this task make take time. If the tool call fails due to a connection error, try retrieving the screen later with `mcp_StitchMCP_list_screens` or `mcp_StitchMCP_get_screen`.

3. **Iterate & Refine**:
   - If the user requests changes, use `mcp_StitchMCP_edit_screens` with the `projectId`, `selectedScreenIds`, and a specific `prompt` detailing the edits.
   - If the user wants to see layout variations, use `mcp_StitchMCP_generate_variants` on the best screen.

4. **Presenting to User**:
   - Always describe the generated screens to the user and present any available previews.

## Best Practices
- **Detailed Prompts**: Be highly descriptive in prompts. Mention color palettes (e.g., "dark mode, vibrant neon blue accents"), typography preferences, spacing, and specific sections needed (e.g., "hero section, feature grid, testimonials").
- **Component Specificity**: When editing, mention exactly what needs to change (e.g., "Change the submit button to green" or "Make the header semi-transparent").
- **Review Output**: If `output_components` returns suggestions, present them to the user or iterate automatically if obvious.
